<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\HmoMail;
use App\Models\Batch;
use App\Models\Hmo;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Create order for HMO
     *
     * @param Request $request
     *
     * @return json
     */
    public function store(Request $request)
    {
        // Validate input data
        $request->validate(Order::$rules);

        try {
            // Get the hmo
            $hmo = Hmo::where('code', $request->provider_data['hmo_code'])->firstOrFail();

            // Create or get batch if it exists
            $batch = $this->batch($hmo, $request->provider_data);

            // Create new order
            $order = new Order();
            $order->items = json_encode($request->order);
            $order->provider_name = $request->provider_data['provider_name'];
            $order->encounter_date = $request->provider_data['date'];

            // Save new order
            $batch->orders()->save($order);

            // Send email to HMO
            try {
                $this->sendMail($hmo->email, ['hmo_name' => $hmo->name, 'batch_name' => $order->batch_name, 'provider_name' => $request->provider_data['provider_name']]);
            } catch (\Throwable $th) {
                logger($th);
            }

            return response()->json(['success' => true, 'message' => 'Order created successfully!']);
        } catch (\Throwable $th) {
            // Log error to console
            logger($th);

            // Return appropriate error message to client
            return response()->json(['success' => false, 'message' => 'Oops! This action cannot be carried out at the moment.'], 500);
        }
    }

    /**
     * Constructs the name of a batch for a particular order
     *
     * @param $hmo
     * @param $provider
     *
     */
    public function batch($hmo, $provider)
    {
        // Determine the batch based on the HMO's preference
        if ($hmo->batch_by_encounter == 1) {
            $encounterDate = Carbon::createFromFormat('Y-m-d', $provider['date']);
            $batchName = $provider['provider_name'] . " " . $encounterDate->format('F') . " " . $encounterDate->format('Y');
        } else {
            $currentDate = Carbon::now();
            $batchName = $provider['provider_name'] . " " . $currentDate->format('M') . " " . $currentDate->format('Y');
        }
        return Batch::firstOrCreate(
            ['name' => $batchName],
            ['name' => $batchName, 'hmo_id' => $hmo->id]
        );
    }

    /**
     * Send email to respective HMOs
     *
     * @param string $hmoEmail
     * @param array $emailData
     *
     * @return void
     */
    public function sendMail($hmoEmail, $emailData)
    {
        Mail::to($hmoEmail)->send(new HmoMail($emailData));
    }

    /**
     * Get batch for HMOs
     *
     * @param $hmoCode
     * @param $dateTime
     * @param $providerName
     *
     * @return $batch
     */
    public function getBatch($hmoCode, $providerName, $dateTime = null)
    {
        try {
            // Get the hmo
            $hmo = Hmo::where('code', $hmoCode)->firstOrFail();

            // Decides if batch is based on encounter date or not
            if ($hmo->batch_by_encounter == 1 && $dateTime != null) {
                $encounterDate = Carbon::createFromFormat('Y-m-d', $dateTime);
                $batchName = $providerName . " " . $encounterDate->format('F') . " " . $encounterDate->format('Y');
            } else {
                $currentDate = Carbon::now();
                $batchName = $providerName . " " . $currentDate->format('M') . " " . $currentDate->format('Y');
            }

            // Get batch
            $batch = Batch::where([['name', '=', $batchName], ['hmo_id', '=', $hmo->id]])->firstOrFail();

            return response()->json(['success' => true, 'data' => $batch]);
        } catch (\Throwable $th) {
            // Log error to console
            logger($th);

            // Return appropriate error message to client
            return response()->json(['success' => false, 'message' => 'Oops! This action cannot be carried out at the moment.'], 500);
        }
    }
}
