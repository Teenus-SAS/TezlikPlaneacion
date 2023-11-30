<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class ProductsDao
{
  private $logger;

  public function __construct()
  {
    $this->logger = new Logger(self::class);
    $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
  }

  public function findAllProductsByCompany($id_company)
  {
    $connection = Connection::getInstance()->getConnection();
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, p.product AS descript, p.img, p.quantity, (SELECT IFNULL(SUM(original_quantity), 0) FROM plan_orders WHERE id_product = p.id_product AND status = 'Despacho') AS reserved, p.classification, 'UNIDAD' AS unit, IFNULL(u.jan , 0) AS jan, 
                                         IFNULL(u.feb, 0) AS feb, IFNULL(u.mar, 0) AS mar, IFNULL(u.apr, 0) AS apr, IFNULL(u.may, 0) AS may, IFNULL(u.jun, 0) AS jun, IFNULL(u.jul, 0) AS jul, IFNULL(u.aug, 0) AS aug, IFNULL(u.sept, 0) AS sept, IFNULL(u.oct, 0) AS oct, IFNULL(u.nov, 0) AS nov, IFNULL(u.dece, 0) AS dece
                                  FROM products p
                                  LEFT JOIN plan_unit_sales u ON u.id_product = p.id_product 
                                  WHERE p.id_company = :id_company");
    $stmt->execute(['id_company' => $id_company]);

    $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    $products = $stmt->fetchAll($connection::FETCH_ASSOC);
    $this->logger->notice("products", array('products' => $products));
    return $products;
  }

  /* Insertar producto */
  public function insertProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $quantity = str_replace('.', '', $dataProduct['quantity']);
      $quantity = str_replace(',', '.', $quantity);

      $stmt = $connection->prepare("INSERT INTO products (id_company, reference, product, quantity) 
                                      VALUES(:id_company, :reference, :product, :quantity)");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'id_company' => $id_company,
        'quantity' => $quantity,
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();

      if ($e->getCode() == 23000)
        $message = 'La referencia ya existe. Ingrese una nueva referencia';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  /* Actualizar producto */
  public function updateProductByCompany($dataProduct, $id_company)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $quantity = str_replace('.', '', $dataProduct['quantity']);
      $quantity = str_replace(',', '.', $quantity);

      $stmt = $connection->prepare("UPDATE products SET reference = :reference, product = :product, quantity = :quantity 
                                    WHERE id_product = :id_product AND id_company = :id_company");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'id_company' => $id_company,
        'quantity' => $quantity,
        'id_product' => $dataProduct['idProduct'],
      ]);
      $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
    } catch (\Exception $e) {
      $message = $e->getMessage();
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }

  public function deleteProduct($id_product)
  {
    $connection = Connection::getInstance()->getConnection();

    try {
      $stmt = $connection->prepare("SELECT * FROM products WHERE id_product = :id_product");
      $stmt->execute(['id_product' => $id_product]);
      $rows = $stmt->rowCount();

      if ($rows > 0) {
        $stmt = $connection->prepare("DELETE FROM products WHERE id_product = :id_product");
        $stmt->execute(['id_product' => $id_product]);
        $this->logger->info(__FUNCTION__, array('query' => $stmt->queryString, 'errors' => $stmt->errorInfo()));
      }
    } catch (\Exception $e) {
      $message = $e->getMessage();
      if ($e->getCode() == 23000)
        $message = 'No es posible eliminar, el producto esta asociado a cotización';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
