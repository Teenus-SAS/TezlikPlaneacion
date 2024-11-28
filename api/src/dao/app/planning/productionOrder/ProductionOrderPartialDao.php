<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductionOrderPartialDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllOPPartialByCompany($id_company)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            pg.num_production,
                                            pg.id_order,
                                            po.id_part_deliv, 
                                            po.id_programming, 
                                            p.id_product, 
                                            p.reference, 
                                            p.product, 
                                            p.origin,
                                            IFNULL(pi.quantity, 0) AS quantity_product, 
                                            po.start_date, 
                                            po.end_date, 
                                            po.operator, 
                                            u.firstname, 
                                            u.lastname, 
                                            po.waste, 
                                            po.partial_quantity, 
                                            po.receive_date, 
                                            IFNULL(last_user.id_user_receive, 0) AS id_user_receive,
                                            IFNULL(last_user.firstname_receive, '') AS firstname_receive,
                                            IFNULL(last_user.lastname_receive, '') AS lastname_receive
                                      FROM prod_order_part_deliv po
                                        INNER JOIN users u ON u.id_user = po.operator
                                        INNER JOIN programming pg ON pg.id_programming = po.id_programming
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        LEFT JOIN inv_products pi ON pi.id_product = pg.id_product
                                            -- Subconsulta para obtener el último usuario de entrega
                                        LEFT JOIN(
                                            SELECT cur.id_part_deliv,
                                                curd.id_user AS id_user_receive,
                                                curd.firstname AS firstname_receive,
                                                curd.lastname AS lastname_receive
                                            FROM prod_order_part_deliv_users cur
                                            INNER JOIN users curd ON curd.id_user = cur.id_user_receive 
                                            WHERE cur.id_part_deliv = (
                                                    SELECT MAX(cur_inner.id_part_deliv)
                                                    FROM prod_order_part_deliv_users cur_inner
                                                    WHERE cur_inner.id_part_deliv = cur.id_part_deliv
                                            )
                                        ) AS last_user ON last_user.id_part_deliv = po.id_part_deliv
                                      WHERE po.id_company = :id_company
                                      ORDER BY `pg`.`num_production` DESC, `po`.`receive_date` ASC");
        $stmt->execute([
            'id_company' => $id_company
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function findAllOPPartialById($id_programming, $id_company, $id_user)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                            po.id_part_deliv, 
                                            po.id_programming, 
                                            p.id_product, 
                                            p.reference, 
                                            p.product,
                                            p.origin, 
                                            IFNULL(pi.quantity, 0) AS quantity_product, 
                                            po.start_date, 
                                            po.end_date,
                                            IFNULL(py.minute_value, 0) AS minute_value,
                                            m.minute_depreciation,
                                            po.operator, 
                                            u.firstname, 
                                            u.lastname, 
                                            po.waste, 
                                            po.partial_quantity, 
                                            po.creation_date,
                                            po.receive_date, 
                                            IFNULL(last_user.id_user_receive, 0) AS id_user_receive,
                                            IFNULL(last_user.firstname_receive, '') AS firstname_receive,
                                            IFNULL(last_user.lastname_receive, '') AS lastname_receive
                                      FROM prod_order_part_deliv po
                                        INNER JOIN users u ON u.id_user = po.operator
                                        INNER JOIN programming pg ON pg.id_programming = po.id_programming
                                        INNER JOIN machines m ON m.id_machine = pg.id_machine
                                        INNER JOIN machine_cicles pcm ON pcm.id_product = pg.id_product AND pcm.id_machine = pg.id_machine
                                        INNER JOIN users ul ON ul.id_user = :id_user
                                        LEFT JOIN payroll py ON py.id_process = pcm.id_process AND py.id_machine = pg.id_machine AND py.firstname = UPPER(ul.firstname) AND py.lastname = UPPER(ul.lastname)
                                        INNER JOIN products p ON p.id_product = pg.id_product
                                        LEFT JOIN inv_products pi ON pi.id_product = pg.id_product
                                        -- Subconsulta para obtener el último usuario de entrega
                                        LEFT JOIN(
                                            SELECT cur.id_part_deliv,
                                                curd.id_user AS id_user_receive,
                                                curd.firstname AS firstname_receive,
                                                curd.lastname AS lastname_receive
                                            FROM prod_order_part_deliv_users cur
                                            INNER JOIN users curd ON curd.id_user = cur.id_user_receive 
                                            WHERE cur.id_part_deliv = (
                                                    SELECT MAX(cur_inner.id_part_deliv)
                                                    FROM prod_order_part_deliv_users cur_inner
                                                    WHERE cur_inner.id_part_deliv = cur.id_part_deliv
                                            )
                                        ) AS last_user ON last_user.id_part_deliv = po.id_part_deliv
                                      WHERE po.id_programming = :id_programming AND po.id_company = :id_company
                                        GROUP BY pg.id_programming, po.id_part_deliv");
        $stmt->execute([
            'id_programming' => $id_programming,
            'id_company' => $id_company,
            'id_user' => $id_user,
        ]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));

        $programming = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $programming;
    }

    public function insertOPPartialByCompany($dataProgramming, $id_company)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("INSERT INTO prod_order_part_deliv (id_company, id_programming, start_date, end_date, operator, waste, partial_quantity)
                                          VALUES (:id_company, :id_programming, :start_date, :end_date, :operator, :waste, :partial_quantity)");
            $stmt->execute([
                'id_company' => $id_company,
                'id_programming' => $dataProgramming['idProgramming'],
                'start_date' => $dataProgramming['startDate'],
                'end_date' => $dataProgramming['endDate'],
                'operator' => $dataProgramming['operator'],
                'waste' => $dataProgramming['waste'],
                'partial_quantity' => $dataProgramming['partialQuantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function updateOPPartial($dataProgramming)
    {
        try {
            $connection = Connection::getInstance()->getConnection();

            $stmt = $connection->prepare("UPDATE prod_order_part_deliv SET start_date = :start_date, end_date = :end_date, waste = :waste, partial_quantity = :partial_quantity
                                          WHERE id_part_deliv = :id_part_deliv");
            $stmt->execute([
                'id_part_deliv' => $dataProgramming['idPartDeliv'],
                'start_date' => $dataProgramming['startDate'],
                'end_date' => $dataProgramming['endDate'],
                'waste' => $dataProgramming['waste'],
                'partial_quantity' => $dataProgramming['partialQuantity']
            ]);
        } catch (\Exception $e) {
            return ['info' => true, 'message' => $e->getMessage()];
        }
    }

    public function deleteOPPartial($id_part_deliv)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM prod_order_part_deliv WHERE id_part_deliv = :id_part_deliv");
        $stmt->execute(['id_part_deliv' => $id_part_deliv]);
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $stmt = $connection->prepare("DELETE FROM prod_order_part_deliv WHERE id_part_deliv = :id_part_deliv");
            $stmt->execute(['id_part_deliv' => $id_part_deliv]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        }
    }

    public function updateDateReceive($dataProgramming)
    {
        $connection = Connection::getInstance()->getConnection();

        try {
            $stmt = $connection->prepare("UPDATE prod_order_part_deliv SET receive_date = :receive_date WHERE id_part_deliv = :id_part_deliv");
            $stmt->execute([
                'id_part_deliv' => $dataProgramming['idPartDeliv'],
                'receive_date' => $dataProgramming['date'],
            ]);
            $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = array('info' => true, 'message' => $message);
            return $error;
        }
    }
}
