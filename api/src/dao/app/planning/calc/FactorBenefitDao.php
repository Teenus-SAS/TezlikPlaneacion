<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class FactorBenefitDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcFactorBenefit($dataBenefits, $dataPayroll)
    {
        try {
            if (
                $dataPayroll['typeFactor'] == '1' || $dataPayroll['typeFactor'] == '2' ||
                $dataPayroll['typeFactor'] == 'Nomina' || $dataPayroll['typeFactor'] == 'Servicios'
            ) {
                $valueBenefits = ($dataPayroll['salary'] + $dataPayroll['extraTime']) * (floatval($dataPayroll['valueRisk']) / 100);

                if ($dataPayroll['typeFactor'] == '1' || $dataPayroll['typeFactor'] == 'Nomina')
                    for ($i = 0; $i < count($dataBenefits); $i++) {
                        $valueBenefit = 0;

                        $benefitPercentage = floatval($dataBenefits[$i]['percentage']) / 100;

                        if ($dataBenefits[$i]['id_benefit'] == '1' || $dataBenefits[$i]['id_benefit'] == '3') {
                            $valueBenefit = ($dataPayroll['salary'] + $dataPayroll['extraTime']) * $benefitPercentage;
                        } else if ($dataBenefits[$i]['id_benefit'] == '2' && $dataPayroll['basicSalary'] > 1160000 * 10) {
                            $valueBenefit = ($dataPayroll['salary'] + $dataPayroll['extraTime']) * $benefitPercentage;
                        } else if ($dataBenefits[$i]['id_benefit'] == '4') {
                            $valueBenefit = ($dataPayroll['salary'] + $dataPayroll['endowment'] + $dataPayroll['transport']) * $benefitPercentage;
                        } else if ($dataBenefits[$i]['id_benefit'] == '5') {
                            $valueBenefit = ($dataPayroll['salary'] + $dataPayroll['extraTime'] + $dataPayroll['transport']) * $benefitPercentage;
                        } else if ($dataBenefits[$i]['id_benefit'] == '6') {
                            $valueBenefit = ($dataPayroll['salary'] + $dataPayroll['transport'] + $dataPayroll['extraTime']) * $benefitPercentage;
                        } else if ($dataBenefits[$i]['id_benefit'] == '7') {
                            $valueBenefit = $dataPayroll['salary'] * $benefitPercentage;
                        }

                        $valueBenefits += $valueBenefit;
                    }
            } else {
                $valueBenefits = ($dataPayroll['salary'] + $dataPayroll['transport']) * ($dataPayroll['factor'] / 100);
            }

            $dataPayroll['factor'] = $valueBenefits;
            return $dataPayroll;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
