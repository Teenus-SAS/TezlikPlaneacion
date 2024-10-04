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
        /*SELECT
                                        -- Columnas
                                            pg.id_programming,
                                            pg.num_production,
                                            o.id_order,
                                            o.num_order,
                                            m.id_material,
                                            m.reference, 
                                            mi.delivery_date,
                                            m.material,
                                            mi.quantity,
                                            u.abbreviation,
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
                                           SELECT 
                                            	cur.id_user_store,
                                            	cur.id_programming,
                                            	cur.id_material, 
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
                                                    SELECT MAX(cur_inner.id_programming)
                                                    FROM store_users cur_inner
                                                    WHERE cur_inner.id_programming = cur.id_programming
                                           )
                                      ) AS last_user ON last_user.id_material = m.id_material AND last_user.id_programming = pg.id_programming
                                      WHERE pg.id_company = 4 AND pg.status = 1
                                      GROUP BY
                                        pg.id_programming,
                                        o.id_order,
                                        o.num_order,
                                        m.id_material,
                                        m.reference,
                                        m.material,
                                        mi.quantity,
                                        u.unit
                                      ORDER BY pg.num_production ASC;
                            SELECT
                        pg.id_programming,
                        m.id_material,
                        IFNULL(SUM(DISTINCT pg.quantity * pm.quantity), 0) AS reserved,
                        IFNULL(SUM(stu.delivery_store), 0) AS delivery_store,
                        IFNULL(MIN(stu.delivery_pending), 0) AS delivery_pending
                    FROM programming pg
                    INNER JOIN products_materials pm ON pm.id_product = pg.id_product
                    INNER JOIN materials m ON m.id_material = pm.id_material
                    LEFT JOIN store_users stu ON stu.id_programming = pg.id_programming AND stu.id_material = m.id_material
                    WHERE pg.id_company = 4 AND pg.status = 1
                    GROUP BY pg.id_programming, m.id_material
                    ORDER BY pg.num_production ASC; */
        $stmt = $connection->prepare("SELECT
                                            -- Columnas originales
                                                pg.id_programming,
                                                pg.num_production,
                                                o.id_order,
                                                o.num_order,
                                                m.id_material,
                                                m.reference, 
                                                mi.delivery_date,
                                                m.material,
                                                mi.quantity,
                                                u.abbreviation,
                                                IFNULL(last_user.id_user_delivered, 0) AS id_user_delivered,
                                                IFNULL(last_user.firstname_delivered, '') AS firstname_delivered,
                                                IFNULL(last_user.lastname_delivered, '') AS lastname_delivered,
                                            -- Nueva subconsulta para evitar duplicados en la suma de reserved1
                                                IFNULL(IF(
                                                    -- Subconsulta para obtener el valor de delivery_pending
                                                    IFNULL((SELECT MIN(stu_sub.delivery_pending)
                                                    FROM store_users stu_sub
                                                    WHERE stu_sub.id_programming = pg.id_programming
                                                    AND stu_sub.id_material = m.id_material), 0) = 0,
                                                    -- Si delivery_pending es 0, calcular la suma de quantity * pm.quantity
                                                    IFNULL((
                                                        SELECT SUM(DISTINCT pg_inner.quantity * pm_inner.quantity)
                                                        FROM products_materials pm_inner
                                                        INNER JOIN programming pg_inner ON pg_inner.id_product = pm_inner.id_product
                                                        WHERE pg_inner.id_programming = pg.id_programming
                                                        AND pm_inner.id_material = m.id_material
                                                    ), 0),
                                                    -- Si delivery_pending no es 0, usar el valor de delivery_pending
                                                    (SELECT MIN(stu_sub.delivery_pending)
                                                    FROM store_users stu_sub
                                                    WHERE stu_sub.id_programming = pg.id_programming
                                                    AND stu_sub.id_material = m.id_material)
                                                ), 0) AS reserved1,
                                            -- Nueva subconsulta para evitar duplicados en la suma de reserved
                                                IFNULL((
                                                    SELECT SUM(DISTINCT pg_inner.quantity * pm_inner.quantity)
                                                    FROM products_materials pm_inner
                                                    INNER JOIN programming pg_inner ON pg_inner.id_product = pm_inner.id_product
                                                    WHERE pg_inner.id_programming = pg.id_programming
                                                    AND pm_inner.id_material = m.id_material
                                                ), 0) AS reserved,
                                            -- Nueva subconsulta para evitar duplicados en la suma de delivery_store
                                                IFNULL((
                                                    SELECT SUM(stu_sub.delivery_store)
                                                    FROM store_users stu_sub
                                                    WHERE stu_sub.id_programming = pg.id_programming
                                                    AND stu_sub.id_material = m.id_material
                                                ), 0) AS delivery_store,
                                            -- Nueva subconsulta para obtener el valor mínimo de delivery_pending
                                                IFNULL((
                                                    SELECT MIN(stu_sub.delivery_pending)
                                                    FROM store_users stu_sub
                                                    WHERE stu_sub.id_programming = pg.id_programming
                                                    AND stu_sub.id_material = m.id_material
                                                ), 0) AS delivery_pending
                                        FROM programming pg
                                        INNER JOIN orders o ON o.id_order = pg.id_order
                                        INNER JOIN products_materials pm ON pm.id_product = pg.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                        INNER JOIN inv_materials mi ON mi.id_material = pm.id_material
                                        INNER JOIN admin_units u ON u.id_unit = m.unit
                                        -- Subconsulta para obtener el último usuario de entrega
                                        LEFT JOIN (
                                            SELECT 
                                                cur.id_user_store,
                                                cur.id_programming,
                                                cur.id_material, 
                                                curd.id_user AS id_user_delivered,
                                                curd.firstname AS firstname_delivered,
                                                curd.lastname AS lastname_delivered
                                            FROM store_users cur
                                            INNER JOIN users curd ON curd.id_user = cur.id_user_delivered 
                                            WHERE cur.id_material = (
                                                    SELECT MAX(cur_inner.id_material)
                                                    FROM store_users cur_inner
                                                    WHERE cur_inner.id_material = cur.id_material
                                            ) 
                                            AND cur.id_programming = (
                                                    SELECT MAX(cur_inner.id_programming)
                                                    FROM store_users cur_inner
                                                    WHERE cur_inner.id_programming = cur.id_programming
                                            )
                                        ) AS last_user ON last_user.id_material = m.id_material AND last_user.id_programming = pg.id_programming
                                        WHERE pg.id_company = :id_company AND pg.status = 1
                                        GROUP BY
                                            pg.id_programming,
                                            pg.num_production,
                                            o.id_order,
                                            o.num_order,
                                            m.id_material,
                                            m.reference, 
                                            mi.delivery_date,
                                            m.material,
                                            mi.quantity,
                                            u.abbreviation,
                                            last_user.id_user_delivered,
                                            last_user.firstname_delivered,
                                            last_user.lastname_delivered
                                        ORDER BY pg.num_production, mi.delivery_date ASC;");
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
