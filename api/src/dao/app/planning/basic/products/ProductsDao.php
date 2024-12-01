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
    $stmt = $connection->prepare("SELECT 
                                        -- Columnas
                                          p.id_product, 
                                          p.reference, 
                                          p.product, 
                                          p.product AS descript, 
                                          p.img, 
                                          IFNULL(pi.id_product_inventory, 0) AS id_product_inventory, 
                                          IFNULL(pi.quantity, 0) AS quantity, 
                                          IFNULL(pi.accumulated_quantity, 0) AS accumulated_quantity, 
                                          IFNULL(pi.reserved, 0) AS reserved, 
                                          IFNULL(pi.minimum_stock, 0) AS minimum_stock, 
                                          IFNULL(pi.days, 0) AS days, 
                                          IFNULL(pi.classification, '') AS classification, 
                                          'UND' AS abbreviation, 
                                          IFNULL(pm.length, 0) AS length, 
                                          IFNULL(pm.total_width, 0) AS total_width, 
                                          p.id_product_type, 
                                          IFNULL(pt.product_type, '') AS product_type, 
                                          p.composite, 
                                          p.origin,
                                          IFNULL((SELECT id_composite_product FROM products_composite WHERE id_product = p.id_product LIMIT 1), 0) AS composite_product,
                                          IFNULL(sp.min_term, '') AS min_term, 
                                          IFNULL(sp.max_term, '') AS max_term
                                  FROM products p
                                    LEFT JOIN inv_products pi ON pi.id_product = p.id_product
                                    LEFT JOIN inv_stock_products sp ON sp.id_product = p.id_product
                                    LEFT JOIN products_measures pm ON pm.id_product = p.id_product
                                    LEFT JOIN products_type pt ON pt.id_product_type = p.id_product_type
                                  WHERE p.id_company = :id_company
                                  ORDER BY p.reference");
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

    !isset($dataProduct['idProductType']) ? $dataProduct['idProductType'] = 0 : $dataProduct;

    try {
      $stmt = $connection->prepare("INSERT INTO products (id_company, id_product_type, reference, product, origin) 
                                      VALUES(:id_company, :id_product_type, :reference, :product, :origin)");
      $stmt->execute([
        'id_product_type' => $dataProduct['idProductType'],
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'origin' => $dataProduct['origin'],
        'id_company' => $id_company,
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

    !isset($dataProduct['idProductType']) ? $dataProduct['idProductType'] = 0 : $dataProduct;

    try {
      $stmt = $connection->prepare("UPDATE products SET id_product_type = :id_product_type, reference = :reference, product = :product, origin = :origin
                                    WHERE id_product = :id_product AND id_company = :id_company");
      $stmt->execute([
        'id_product_type' => $dataProduct['idProductType'],
        'reference' => trim($dataProduct['referenceProduct']),
        'product' => strtoupper(trim($dataProduct['product'])),
        'origin' => $dataProduct['origin'],
        'id_company' => $id_company,
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
      // if ($e->getCode() == 23000)
      //   $message = 'No es posible eliminar, el producto esta asociado a cotización';
      $error = array('info' => true, 'message' => $message);
      return $error;
    }
  }
}
