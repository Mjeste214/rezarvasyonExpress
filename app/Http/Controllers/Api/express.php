<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainRequest;

class express extends Controller
{
    public $RezervasyonYapilabilir = false;
    public $YerlesimAyrinti = [];

    public function isAvaible(TrainRequest $request)
    {
        $response = [];

        if ($request->KisilerFarkliVagonlaraYerlestirilebilir) {
            $this->randomSet($request->Tren, $request->RezervasyonYapilacakKisiSayisi);
        } else {
            $this->ordinarySet($request->Tren, $request->RezervasyonYapilacakKisiSayisi);
        }
        $response['RezervasyonYapilabilir'] = $this->RezervasyonYapilabilir;
        $response['YerlesimAyrinti'] = $this->YerlesimAyrinti;

        return response()->json($response);
    }

    private function randomSet($datas, $rezarvasyonkisisayisi)
    {
        $toplamMusaitKoltukSayisi = 0;
        $musaitVagonlar = [];
        $isok = false;

        foreach ($datas['Vagonlar'] as $data) {
            if ($this->controlRatio($data)) {
                $musaitKoltukSayisi = $data['Kapasite'] - $data['DoluKoltukAdet'];

                $toplamMusaitKoltukSayisi = $toplamMusaitKoltukSayisi + $musaitKoltukSayisi;

                if ($musaitKoltukSayisi > 0) {
                    $musaitVagonlar[] = $data['Ad'];
                }
            }
        }

        if ($toplamMusaitKoltukSayisi >= $rezarvasyonkisisayisi) {
            $this->RezervasyonYapilabilir = true;
            $this->YerlesimAyrinti = $musaitVagonlar;

        } else {
            $this->RezervasyonYapilabilir = false;
            $this->YerlesimAyrinti = [];
        }
    }

    private function ordinarySet($datas, $rezarvasyonkisisayisi)
    {
        $toplamMusaitKoltukSayisi = 0;
        $musaitVagonlar = [];

        foreach ($datas['Vagonlar'] as $data) {
            if ($this->controlRatio($data)) {

                $musaitKoltukSayisi = $data['Kapasite'] - $data['DoluKoltukAdet'];
                $toplamMusaitKoltukSayisi = $toplamMusaitKoltukSayisi + $musaitKoltukSayisi;

                if ($musaitKoltukSayisi >= $rezarvasyonkisisayisi) {
                    $musaitVagonlar[] = $data['Ad'];
                    break;
                }
            }
        }

        if (count($musaitVagonlar) > 0) {
            $this->RezervasyonYapilabilir = true;
            $this->YerlesimAyrinti = $musaitVagonlar;
        } else {
            $this->RezervasyonYapilabilir = false;
            $this->YerlesimAyrinti = [];
        }
    }

    private function controlRatio($data): bool
    {
        if ((($data['Kapasite'] * 70) / 100) > $data['DoluKoltukAdet']) {
            return true;
        } else {
            return false;
        }

    }
}
