<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductionOrderMPDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllOPMaterialByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            pom.id_prod_order_material, 
                                            pom.id_programming, 
                                            m.id_material, 
                                            m.reference, 
                                            m.material, 
                                            IFNULL(mi.quantity, 0) AS quantity_material,
                                            pom.quantity
                                            -- IFNULL(last_user.id_user_receive, 0) AS id_user_receive,
                                            -- IFNULL(last_user.firstname_receive, '') AS firstname_receive,
                                            -- IFNULL(last_user.lastname_receive, '') AS lastname_receive
                                      FROM prod_order_materials pom 
                                        INNER JOIN programming pg ON pg.id_programming = pom.id_programming
                                        INNER JOIN materials m ON m.id_material = pom.id_material
                                        LEFT JOIN inv_materials mi ON mi.id_material = pom.id_material
                                            -- Subconsulta para obtener el último usuario de entrega
                                        -- LEFT JOIN(
                                        --     SELECT cur.id_prod_order_material,
                                        --         curd.id_user AS id_user_receive,
                                        --         curd.firstname AS firstname_receive,
                                        --         curd.lastname AS lastname_receive
                                        --     FROM prod_order_materials_users cur
                                        --     INNER JOIN users curd ON curd.id_user = cur.id_user_receive 
                                        --     WHERE cur.id_prod_order_material = (
                                        --             SELECT MAX(cur_inner.id_prod_order_material)
                                        --             FROM prod_order_materials_users cur_inner
                                        --             WHERE cur_inner.id_prod_order_material = cur.id_prod_order_material
                                        --     )
                                        -- ) AS last_user ON last_user.id_prod_order_material = m.id_prod_order_material
                                      WHERE pom.id_company = :id_company");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findAllOPMaterialById($id_programming, $id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            pom.id_prod_order_material, 
                                            pom.id_programming, 
                                            m.id_material, 
                                            m.reference, 
                                            m.material, 
                                            IFNULL(mi.quantity, 0) AS quantity_material,
                                            IFNULL(mi.delivery_date, '0000-00-00 00:00:00') AS delivery_date,
                                            pom.quantity,
                                            IFNULL(last_user.id_user_delivered, 0) AS id_user_delivered,
                                            IFNULL(last_user.firstname_delivered, '') AS firstname_delivered,
                                            IFNULL(last_user.lastname_delivered, '') AS lastname_delivered
                                      FROM prod_order_materials pom 
                                        INNER JOIN programming pg ON pg.id_programming = pom.id_programming
                                        INNER JOIN materials m ON m.id_material = pom.id_material
                                        LEFT JOIN inv_materials mi ON mi.id_material = pom.id_material 
                                        -- Subconsulta para obtener el último usuario de entrega
                                        LEFT JOIN(
                                            SELECT cur.id_material,
                                                cur.id_programming,
                                                curd.id_user AS id_user_delivered,
                                                curd.firstname AS firstname_delivered,
                                                curd.lastname AS lastname_delivered
                                            FROM store_users cur
                                            INNER JOIN users curd ON curd.id_user = cur.id_user_delivered 
                                            WHERE cur.id_material = (
                                                    SELECT MAX(cur_inner.id_material)
                                                    FROM store_users cur_inner
                                                    WHERE cur_inner.id_material = cur.id_material
                                            ) AND
                                            cur.id_programming = (
                                                    SELECT MAX(cur_inner.id_material)
                                                    FROM store_users cur_inner
                                                    WHERE cur_inner.id_programming = cur.id_programming
                                            )
                                        ) AS last_user ON last_user.id_material = pom.id_material AND last_user.id_programming = pom.id_programming
                                      WHERE pom.id_programming = :id_programming AND pom.id_company = :id_company");
        $stmt->execute([
            'id_programming' => $id_programming,
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function insertOPMaterialByCompany($dataProgramming, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO prod_order_materials (id_company, id_programming, id_material, quantity)
                                          VALUES (:id_company, :id_programming, :id_material, :quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_programming' => $dataProgramming['idProgramming'],
                'id_material' => $dataProgramming['idMaterial'],
                'quantity' => $dataProgramming['quantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateOPMaterial($dataProgramming)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE prod_order_materials SET id_material = :id_material, quantity = :quantity
                                          WHERE id_prod_order_material = :id_prod_order_material");
            $stmt->execute([
                'id_prod_order_material' => $dataProgramming['idOPM'],
                'id_material' => $dataProgramming['idMaterial'],
                'quantity' => $dataProgramming['quantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteOPMaterial($id_prod_order_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM prod_order_materials WHERE id_prod_order_material = :id_prod_order_material");
        $stmt->execute(['id_prod_order_material' => $id_prod_order_material]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM prod_order_materials WHERE id_prod_order_material = :id_prod_order_material");
            $stmt->execute(['id_prod_order_material' => $id_prod_order_material]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }

    // public function updateDateReceive($dataProgramming)
    // {
    //     $connection = Connection::getInstance()->getConnection();

    //     try {
    //         $stmt = $connection->prepare("UPDATE prod_order_materials SET receive_date = :receive_date WHERE id_prod_order_material = :id_prod_order_material");
    //         $stmt->execute([
    //             'id_prod_order_material' => $dataProgramming['idPartDeliv'],
    //             'receive_date' => $dataProgramming['date'],
    //         ]);
    //         $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    //     } catch (\Exception $e) {
    //         $message = $e->getMessage();
    //         $error = array('info' => true, 'message' => $message);
    //         return $error;
    //     }
    // }
}