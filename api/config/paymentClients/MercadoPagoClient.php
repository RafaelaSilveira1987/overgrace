<?php


class MercadoPagoClient
{
    private string $accessToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->accessToken = getenv('MP_ACCESS_TOKEN');

        // Hoje a API é a mesma.
        // Mantive separado caso o MP mude no futuro.
        $this->baseUrl = "https://api.mercadopago.com";
    }

    /**
     * Cria pagamento PIX
     */
    public function createPix(array $payload)
    {
        return $this->request(
            "POST",
            "/v1/payments",
            $payload
        );
    }

    /**
     * Consulta pagamento
     */
    public function getPayment($paymentId)
    {
        return $this->request(
            "GET",
            "/v1/payments/{$paymentId}"
        );
    }

    /**
     * Cancela pagamento
     */
    public function cancelPayment($paymentId)
    {
        return $this->request(
            "PUT",
            "/v1/payments/{$paymentId}",
            [
                "status"=>"cancelled"
            ]
        );
    }

    /**
     * HTTP
     */
    private function request(
        string $method,
        string $endpoint,
        array $body = null
    )
    {

        $curl = curl_init();

        curl_setopt_array($curl,[

            CURLOPT_URL=>$this->baseUrl.$endpoint,

            CURLOPT_RETURNTRANSFER=>true,

            CURLOPT_CUSTOMREQUEST=>$method,

            CURLOPT_HTTPHEADER=>[

                "Authorization: Bearer {$this->accessToken}",

                "Content-Type: application/json",

                "X-Idempotency-Key: ".uniqid()

            ]

        ]);

        if($body){

            curl_setopt(
                $curl,
                CURLOPT_POSTFIELDS,
                json_encode($body)
            );

        }

        $response = curl_exec($curl);

        $status = curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        if(curl_errno($curl)){

            throw new \Exception(
                curl_error($curl)
            );

        }

        curl_close($curl);

        $json = json_decode(
            $response,
            true
        );

        if($status>=400){

            throw new \Exception(

                $json["message"] ??

                "Erro Mercado Pago"

            );

        }

        return $json;

    }

}