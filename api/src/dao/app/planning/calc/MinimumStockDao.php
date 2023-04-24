<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MinimumStockDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function calcMinimumStock($dataStock, $id_company)
    {
        $connection = Connection::getInstance()->getconnection();
        $stmt = $connection->prepare("SELECT 
                                            (((us.jan + us.feb + us.mar + us.apr + us.may + us.jun + us.jul + us.aug + us.sept + us.oct + us.nov + us.dece) / 
                                            (ppm.january + ppm.february + ppm.march + ppm.april + ppm.may + ppm.june + ppm.july + ppm.august + ppm.september + ppm.october + ppm.november + ppm.december)) * pp.lead_time) AS minimum_stock 
                                      FROM plan_unit_sales us 
                                        INNER JOIN plan_cicles_machine cm ON cm.id_product = us.id_product 
                                        INNER JOIN plan_program_machines ppm ON ppm.id_machine = cm.id_machine 
                                        INNER JOIN products_materials pm ON pm.id_product = us.id_product
                                        INNER JOIN products_providers pp ON pp.id_material = pm.id_material
                                      WHERE us.id_product = :id_product AND us.id_company = :id_company");
        $stmt->execute([
            'id_product' => $dataStock['idProduct'],
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        $minimumStock = $stmt->fetch($connection::FETCH_ASSOC);
        return $minimumStock;
    }
}
