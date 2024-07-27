<?php

namespace TezlikPlaneacion\Dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ConversionUnitsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function convertUnits($dataMaterial, $dataProductMaterial, $quantity)
    {
        try {
            $magnitude = strtoupper(trim($dataProductMaterial['magnitude']));
            $unitProductMaterial = strtoupper(trim($dataProductMaterial['abbreviation']));
            $unitMaterial = $dataMaterial['abbreviation'];

            $arr = [];

            if (empty($magnitude) || empty($unitProductMaterial) || empty($unitMaterial))
                return $quantity;

            if ($unitProductMaterial != $unitMaterial && $magnitude != 'UNIDAD') {
                switch ($magnitude) {
                    case 'LONGITUD':
                        $arr['M'] = array(
                            'CM' => array('value' => 0.01), 'ML' => array('value' => 0.001),
                            'INCH' => array('value' => 0.0254), 'FT' => array('value' => 0.3048)
                        );
                        $arr['CM'] = array(
                            'M' => array('value' => 100), 'ML' => array('value' => 0.1),
                            'INCH' => array('value' => 2.54), 'FT' => array('value' => 30.48)
                        );
                        $arr['ML'] = array(
                            'M' => array('value' => 1000), 'CM' => array('value' => 10),
                            'INCH' => array('value' => 25.4), 'FT' => array('value' => 304.8)
                        );
                        $arr['INCH'] = array(
                            'M' => array('value' => 39.37007874), 'CM' => array('value' => 0.3937007874),
                            'ML' => array('value' => 0.0393700787), 'FT' => array('value' => 12)
                        );
                        $arr['FT'] = array(
                            'M' => array('value' => 3.280839895), 'CM' => array('value' => 0.032808399),
                            'ML' => array('value' => 0.0032808399), 'INCH' => array('value' => 0.0833333333)
                        );
                        break;
                    case 'MASA':
                        $arr['TN'] = array(
                            'KG' => array('value' => 0.001), 'GR' => array('value' => 0.000001),
                            'MG' => array('value' => 0.000000001), 'LB' => array('value' => 0.0004535924)
                        );
                        $arr['KG'] = array(
                            'TN' => array('value' => 1000), 'GR' => array('value' => 0.001),
                            'MG' => array('value' => 0.000001), 'LB' => array('value' => 0.45359237)
                        );
                        $arr['GR'] = array(
                            'TN' => array('value' => 1000000), 'KG' => array('value' => 1000),
                            'MG' => array('value' => 0.001), 'LB' => array('value' => 453.59237)
                        );
                        $arr['MG'] = array(
                            'TN' => array('value' => 1000000000), 'KG' => array('value' => 1000000),
                            'GR' => array('value' => 1000), 'LB' => array('value' => 453592.37)
                        );
                        $arr['LB'] = array(
                            'TN' => array('value' => 2204.6226218), 'KG' => array('value' => 2.2046226218),
                            'GR' => array('value' => 0.0022046226), 'MG' => array('value' => 0.0000022046)
                        );
                        break;
                    case 'VOLUMEN':
                        $arr['CM3'] = array(
                            'M3' => array('value' => 1000000), 'L' => array('value' => 1000),
                            'ML' => array('value' => 1), 'GL' => array('value' => 3785.41)
                        );
                        $arr['M3'] = array(
                            'CM3' => array('value' => 0.000001), 'L' => array('value' => 0.001),
                            'ML' => array('value' => 0.000001), 'GL' => array('value' => 0.00378541)
                        );
                        $arr['L'] = array(
                            'CM3' => array('value' => 0.001), 'M3' => array('value' => 1000),
                            'ML' => array('value' => 0.001), 'GL' => array('value' => 3.78541)
                        );
                        $arr['ML'] = array(
                            'CM3' => array('value' => 1), 'M3' => array('value' => 1000000),
                            'L' => array('value' => 1000), 'GL' => array('value' => 3785.41)
                        );
                        $arr['GL'] = array(
                            'CM3' => array('value' => 0.000264172, 'op' => '/'), 'M3' => array('value' => 264.172),
                            'L' => array('value' => 0.264172), 'ML' => array('value' => 0.000264172)
                        );
                        break;
                    case 'ÁREA':
                        $arr['DM2'] = array(
                            'M2' => array('value' => 100), 'FT2' => array('value' => 9.2903043597),
                            'INCH2' => array('value' => 0.0645160042)
                        );
                        $arr['M2'] = array(
                            'DM2' => array('value' => 0.01), 'FT2' => array('value' => 0.0929030436),
                            'INCH2' => array('value' => 0.00064516)
                        );
                        $arr['FT2'] = array(
                            'DM2' => array('value' => 0.1076391), 'M2' => array('value' => 10.76391),
                            'INCH2' => array('value' => 0.0069444446)
                        );
                        $arr['INCH2'] = array(
                            'DM2' => array('value' => 15.50003), 'M2' => array('value' => 1550.003),
                            'FT2' => array('value' => 143.99999628)
                        );
                        break;
                }

                $unit = $arr[$unitMaterial][$unitProductMaterial];
                $quantity = $quantity * $unit['value'];
                return $quantity;
            } else
                return $quantity;
        } catch (\Exception $e) {
            $message = $e->getMessage();

            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
