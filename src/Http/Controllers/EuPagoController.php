<?php

namespace CodeTech\EuPago\Http\Controllers;

use CodeTech\EuPago\Events\CCTransactionPaid;
use CodeTech\EuPago\Events\MBReferencePaid;
use CodeTech\EuPago\Events\MBWayReferencePaid;
use CodeTech\EuPago\Http\Requests\CallbackRequest;
use CodeTech\EuPago\Http\Requests\MbCallbackRequest;
use CodeTech\EuPago\Http\Requests\MbWayCallbackRequest;
use CodeTech\EuPago\Models\CCTransaction;
use CodeTech\EuPago\Models\MbReference;
use CodeTech\EuPago\Models\MbwayReference;
use Illuminate\Http\Request;

class EuPagoController extends Controller
{
    public function callback(CallbackRequest $request){
        $data=$request->all();


        if($data["mp"] === "PC:PT"){
            return $this->callAction('mbcallback',[$request]);
        }
        if($data["mp"] === "MW:PT"){
            return $this->callAction('mbwaycallback',[$request]);
        }
        if($data["mp"] === "CC:PT"){
            return $this->callAction('cccallback',[$request]);
        }
    }

    /**
     * This endpoint is called when a MB reference is paid.
     *
     * @param MbCallbackRequest $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function mbcallback(CallbackRequest $request)
    {
        $validatedData = $request->validated();

        $reference = MbReference::where('reference', $validatedData['referencia'])
            ->where('value', $validatedData['valor'])
            ->where('state', 0)
            ->first();

        if (!$reference) {
            return response()->json(['response' => 'No pending reference found'])->setStatusCode(404);
        }

        $reference->update(['state' => 1]);

        // trigger event
        event(new MBReferencePaid($reference));

        return response()->json(['response' => 'Success'])->setStatusCode(200);
    }
    /**
     * This endpoint is called when a MB Way reference is paid.
     *
     * @param MbWayCallbackRequest $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function mbwaycallback(CallbackRequest $request)
    {
        $validatedData = $request->validated();

        $reference = MbwayReference::where('reference', $validatedData['referencia'])
            ->where('value', $validatedData['valor'])
            ->where('state', 0)
            ->first();

        if (!$reference) {
            return response()->json(['response' => 'No pending reference found'])->setStatusCode(404);
        }

        $reference->update(['state' => 1]);

        // trigger event
        event(new MBWayReferencePaid($reference));

        return response()->json(['response' => 'Success'])->setStatusCode(200);
    }

    /**
     * This endpoint is called when a MB Way reference is paid.
     *
     * @param CallbackRequest $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function cccallback(CallbackRequest $request)
    {
        $validatedData = $request->validated();

        $reference = CCTransaction::where('reference', $validatedData['referencia'])
            ->where('value', $validatedData['valor'])
            ->where('state', 0)
            ->first();

        if (!$reference) {
            return response()->json(['response' => 'No pending reference found'])->setStatusCode(404);
        }

        $reference->update(['state' => 1]);

        // trigger event
        event(new CCTransactionPaid($reference));

        return response()->json(['response' => 'Success'])->setStatusCode(200);
    }
}
