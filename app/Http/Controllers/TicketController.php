<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Models\Promo;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpParser\Node\Expr\FuncCall;

class TicketController extends Controller
{

    public function showSeats($scheduleId, $hourId) {
        //dd($scheduleId, $hourId);
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        $hour = $schedule['hours'][$hourId] ?? '-';

        $seats = Ticket::whereHas('ticketPayment', function($q) {
            $q->whereDate('paid_date', now()->format('Y-m-d'));
        })->whereTime('hours', $hour)->pluck('row_of_seats');
        //pluck() mengambil hanya dari 1 field, bedanya dengan value() kalau value ambil data dari field tsb

        $seatsFormat = array_merge(...$seats);
        //dd($seatsFormat);
        return view('schedule.show-seats', compact('schedule', 'hour', 'seatsFormat'));
    }

    public function chartData()
    {
        $month = now()->format('m'); //bulan saat ini
        //ambil data tiket yang sudah dibayar dan di bayar bulan ini, kemudian kelompokan datanya berdasarkan tanggal pembayaran groupby
        $tickets = Ticket::whereHas('ticketPayment', function($q) use($month) {
            $q->whereMonth('paid_date', $month);
        })->get()->groupBy(function($ticket) {
            return \Carbon\Carbon::parse($ticket->ticketPayment->paid_date)->format('Y-m-d');
        })->toArray();

        $labels = array_keys($tickets);

        $data = [];
        foreach ($tickets as $item) {
            array_push($data, count($item));
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticketActive = Ticket::whereHas('ticketPayment', function($q) {
            $q->whereDate('booked_date', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        $ticketNonActive = Ticket::whereHas('ticketPayment', function($q) {
            $q->whereDate('booked_date', '<', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        return view('ticket.index', compact('ticketActive', 'ticketNonActive'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'hours' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'row_of_seats' => 'required',
        ]);

        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'hours' => $request->hours,
            'total_price' => $request->total_price,
            'quantity' => $request->quantity,
            'row_of_seats' => $request->row_of_seats,
            'actived' => 0,
            'date' => now()

        ]);

        return response()->json([
            'message' => 'Berhasil membuat data tiket',
            'data' => $createData
        ]);
    }

    public function ticketOrder($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with('schedule.movie', 'schedule.cinema')->first();
        $promos = Promo::where('actived', 1)->get();
        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function ticketPayment(Request $request)
    {
        $kodeBarcode = 'TICKET'. $request->ticket_id;

        $qrImage = QrCode::format('svg')->size(300)->margin(2)->errorCorrection('H')->generate($kodeBarcode);


        $filename = $kodeBarcode . '.svg';
        $path = 'barcodes/' . $filename;
        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $request->ticket_id,
            'barcode' => $path,
            'status' => 'process',
            'booked_date' => now()
        ]);

        $ticket = Ticket::find($request->ticket_id);
        if ($request->promo_id != NULL) {
            $promo = Promo::find($request->promo_id);
            if($promo['type'] == 'percent'){
                $discount = $ticket['total_price'] * ($promo['discount'] / 100);
            } else {
                $discount = $promo['discount'];
            }
            $totalPrice = $ticket['total_price'] - $discount;
            $updateTicket = Ticket::where('id', $request->ticket_id)->update([
                'promo_id' => $request->promo_id,
                'total_price' => $totalPrice
            ]);
        }

        return response()->json([
            'message' => 'Berhasil membuat pesanan tiket sementara!',
            'data' => $createData
        ]);
    }


    public function ticketPaymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['promo', 'ticketPayment', 'schedule'])->first(); //kenapa first karena mau munculin nya 1 kalau get semua
        return view('schedule.payment', compact('ticket'));
    }

    public function paymentProof($ticketId)
    {
        $updateData = Ticket::where('id', $ticketId)->update([
            'actived' => 1,
        ]);

        $updatePayment = TicketPayment::where('ticket_id', $ticketId)->update([
            'paid_date' =>now()
        ]);

        return redirect()->route('tickets.receipt', $ticketId);

    }

    public function ticketReceipt($ticketId)
    {

        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])->first();
        return view('schedule.receipt', compact('ticket'));

    }

    public function exportPdf($ticketId)
    {
         //siapkan data. data yang di kirim harus berupa array
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'promo', 'ticketPayment'])->first()->toArray();

        //buat inisial nama data yang nani akan di gunakan pada blade pdf
        view()->share('ticket', $ticket);

        $pdf = Pdf::loadView('schedule.export-pdf', $ticket);
        $fileName  = 'TICKET' . $ticket['id'] . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
