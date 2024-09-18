<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class StoreDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllStore($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        // $stmt = $connection->prepare("SELECT pg.id_programming, o.id_order, o.num_order, m.id_material, m.reference, mi.delivery_store, mi.delivery_pending, mi.delivery_date,
        //                                      m.material, mi.quantity, u.abbreviation, IFNULL(SUM(pg.quantity * pm.quantity), 0) AS reserved, pg.quantity AS deliver,
        //                                      IF(mi.delivery_pending = 0, IFNULL(SUM(pg.quantity * pm.quantity), 0) , mi.delivery_pending) AS reserved1, IFNULL(us.id_user, 0) AS id_user_delivered, 
        //                                      IFNULL(us.firstname, '') AS firstname_delivered, IFNULL(us.lastname, '') AS lastname_delivered
        //                               FROM programming pg
        //                                 INNER JOIN orders o ON o.id_order = pg.id_order
        //                                 INNER JOIN products_materials pm ON pm.id_product = pg.id_product
        //                                 INNER JOIN materials m ON m.id_material = pm.id_material
        //                                 INNER JOIN inv_materials mi ON mi.id_material = pm.id_material
        //                                 INNER JOIN admin_units u ON u.id_unit = m.unit 
        //                                 LEFT JOIN users us ON us.id_user = mi.id_user_delivered
        //                               WHERE pg.id_company = :id_company AND pg.status = 1
        //                               GROUP BY pg.id_programming, o.id_order, o.num_order, m.id_material, m.reference, m.material, mi.quantity, u.unit
        //                               ORDER BY o.num_order ASC");
        $stmt = $connection->prepare("SELECT
                                        -- Columnas
                                            pg.id_programming,
                                            o.id_order,
                                            o.num_order,
                                            m.id_material,
                                            m.reference,
                                            mi.delivery_store,
                                            mi.delivery_pending,
                                            mi.delivery_date,
                                            m.material,
                                            mi.quantity,
                                            u.abbreviation,
                                            IFNULL(SUM(pg.quantity * pm.quantity), 0) AS reserved,
                                            pg.quantity AS deliver,
                                            IF(mi.delivery_pending = 0, IFNULL(SUM(pg.quantity * pm.quantity), 0 ), mi.delivery_pending) AS reserved1,
                                            IFNULL(last_user.id_user_delivered, 0) AS id_user_delivered,
                                            IFNULL(last_user.firstname_delivered, '') AS firstname_delivered,
                                            IFNULL(last_user.lastname_delivered, '') AS lastname_delivered
                                      FROM programming pg
                                        INNER JOIN orders o ON o.id_order = pg.id_order
                                        INNER JOIN products_materials pm ON pm.id_product = pg.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN inv_materials mi ON mi.id_material = pm.id_material
                                        INNER JOIN admin_units u ON u.id_unit = m.unit
                                            -- Subconsulta para obtener el último usuario de entrega
                                        LEFT JOIN(
                                            SELECT cur.id_material,
                                                curd.id_user AS id_user_delivered,
                                                curd.firstname AS firstname_delivered,
                                                curd.lastname AS lastname_delivered
                                            FROM store_users cur
                                            INNER JOIN users curd ON curd.id_user = cur.id_user_delivered 
                                            ORDER BY
                                                cur.id_user_store
                                            DESC
                                            LIMIT 1) AS last_user ON last_user.id_material = m.id_material
                                      WHERE pg.id_company = :id_company AND pg.status = 1
                                      GROUP BY
                                        pg.id_programming,
                                        o.id_order,
                                        o.num_order,
                                        m.id_material,
                                        m.reference,
                                        m.material,
                                        mi.quantity,
                                        u.unit
                                      ORDER BY o.num_order ASC");
        $stmt->execute(['id_company' => $id_company]);

        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $store = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $store;
    }

    public function saveDelivery($dataStore, $status)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE inv_materials SET status = :status WHERE id_material = :id_material");
            $stmt->execute([
                'status' => $status,
                'id_material' => $dataStore['idMaterial']
            ]);
        } catch (\Exception $e) {
            $error = array('info' => true, 'message' => $e->getMessage());
            return $error;
        }
    }
}
