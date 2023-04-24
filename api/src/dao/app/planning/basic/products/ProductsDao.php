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
    $stmt = $connection->prepare("SELECT p.id_product, p.reference, p.product, p.img, p.quantity, IFNULL(p.category, 0) AS id_category, c.category, m.mold, p.classification
                                  FROM products p
                                    LEFT JOIN plan_categories c ON c.id_category = p.category
                                    LEFT JOIN plan_inv_molds m ON m.id_mold = p.id_mold
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
      $stmt = $connection->prepare("INSERT INTO products (id_company, reference, product, id_mold, quantity, category) 
                                      VALUES(:id_company, :reference, :product, :id_mold, :quantity, :category)");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'id_mold' => $dataProduct['idMold'],
        'id_company' => $id_company,
        'quantity' => $dataProduct['quantity'],
        'category' => $dataProduct['category']
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
      $stmt = $connection->prepare("UPDATE products SET reference = :reference, product = :product, id_mold = :id_mold, quantity = :quantity, category = :category 
                                    WHERE id_product = :id_product AND id_company = :id_company");
      $stmt->execute([
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'id_mold' => $dataProduct['idMold'],
        'id_company' => $id_company,
        'quantity' => $dataProduct['quantity'],
        'category' => $dataProduct['category'],
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
