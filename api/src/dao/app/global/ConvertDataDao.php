<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConvertDataDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    /* Productos Materias */
    public function strReplaceProductsMaterials($dataProductMaterial)
    {
        $dataProductMaterial['quantity'] = str_replace('.', '', $dataProductMaterial['quantity']);
        $dataProductMaterial['quantity'] = str_replace(',', '.', $dataProductMaterial['quantity']);

        return $dataProductMaterial;
    }

    /* Productos Procesos */
    public function strReplaceProductsProcess($dataProductProcess)
    {
        $dataProductProcess['enlistmentTime'] = str_replace('.', '', $dataProductProcess['enlistmentTime']);
        $dataProductProcess['enlistmentTime'] = str_replace(',', '.', $dataProductProcess['enlistmentTime']);
        $dataProductProcess['operationTime'] = str_replace('.', '', $dataProductProcess['operationTime']);
        $dataProductProcess['operationTime'] = str_replace(',', '.', $dataProductProcess['operationTime']);

        return $dataProductProcess;
    }

    /* Moldes */
    public function strReplaceMold($dataMold)
    {
        $dataMold['assemblyTime'] = str_replace('.', '', $dataMold['assemblyTime']);
        $dataMold['assemblyTime'] = str_replace(',', '.', $dataMold['assemblyTime']);
        $dataMold['assemblyProduction'] = str_replace('.', '', $dataMold['assemblyProduction']);
        $dataMold['assemblyProduction'] = str_replace(',', '.', $dataMold['assemblyProduction']);
        $dataMold['cavity'] = str_replace('.', '', $dataMold['cavity']);
        $dataMold['cavity'] = str_replace(',', '.', $dataMold['cavity']);
        $dataMold['cavityAvailable'] = str_replace('.', '', $dataMold['cavityAvailable']);
        $dataMold['cavityAvailable'] = str_replace(',', '.', $dataMold['cavityAvailable']);

        return $dataMold;
    }

    /* Pedidos */
    public function changeDateOrder($dataOrder)
    {
        $date = str_replace('/', '-', $dataOrder['dateOrder']);
        $minDate = str_replace('/', '-', $dataOrder['minDate']);
        $maxDate = str_replace('/', '-', $dataOrder['maxDate']);
        $dataOrder['dateOrder'] = date('Y-m-d', strtotime($date));
        $dataOrder['minDate'] = date('Y-m-d', strtotime($minDate));
        $dataOrder['maxDate'] = date('Y-m-d', strtotime($maxDate));

        return $dataOrder;
    }
}
