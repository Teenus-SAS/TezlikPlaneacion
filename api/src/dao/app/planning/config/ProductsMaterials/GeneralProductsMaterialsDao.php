<?php

namespace TezlikPlaneacion\dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GeneralProductsMaterialsDao
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
    }

    public function findAllProductsMaterials($id_company)
    {
        $connection = Connection::getInstance()->getConnection();
        $stmt = $connection->prepare("SELECT p.id_product, p.reference AS reference_product, p.product, m.id_material, m.reference AS reference_material, m.material, (1*m.quantity/pm.quantity) AS quantity
                                      FROM products p
                                        INNER JOIN products_materials pm ON pm.id_product = p.id_product
                                        INNER JOIN materials m ON m.id_material = pm.id_material
                                      WHERE pm.id_company = :id_company AND pm.id_material IN (SELECT id_material FROM materials INNER JOIN convert_units ON convert_units.id_unit = materials.unit WHERE id_material = pm.id_material)
                                      ORDER BY `m`.`material` ASC");
        $stmt->execute(['id_company' => $id_company]);
        $productsmaterials = $stmt->fetchAll($connection::FETCH_ASSOC);
        $this->logger->notice("products", array('products' => $productsmaterials));
        return $productsmaterials;
    }

    // Consultar si existe el product_material en la BD
    public function findProductMaterial($dataProductMaterial)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT * FROM products_materials
                                      WHERE id_product = :id_product AND id_material = :id_material");
        $stmt->execute([
            'id_product' => $dataProductMaterial['idProduct'],
            'id_material' => $dataProductMaterial['material']
        ]);
        $findProductMaterial = $stmt->fetch($connection::FETCH_ASSOC);
        return $findProductMaterial;
    }

    // Consultar si existe el product_material en la BD
    public function findAllProductByMaterial($id_material)
    {
        $connection = Connection::getInstance()->getConnection();

        $stmt = $connection->prepare("SELECT pm.id_product_material, pm.id_material, pm.id_product, p.quantity
                                      FROM products_materials pm
                                      INNER JOIN products p ON p.id_product = pm.id_product
                                      WHERE id_material = :id_material");
        $stmt->execute([
            'id_material' => $id_material
        ]);
        $products = $stmt->fetchAll($connection::FETCH_ASSOC);
        return $products;
    }
}
