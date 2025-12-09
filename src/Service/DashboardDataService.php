<?php
/**
 * @author    Daniel Ionașcu
 * @copyright 2025 Daniel Ionașcu
 * @license   MIT
 */

class DashboardDataService
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getMostViewedProducts($days, $limit = 5)
    {
        $idLang = (int)Context::getContext()->language->id;
        $idShop = (int)Context::getContext()->shop->id;

        $sql = 'SELECT p.id_product, pl.name, COUNT(pv.id_product) as total_views
                FROM ' . _DB_PREFIX_ . 'page pv
                JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = pv.id_object
                JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . '
                WHERE pv.page_type = "product"
                AND pv.date_add >= DATE_SUB(NOW(), INTERVAL ' . (int)$days . ' DAY)
                AND pl.id_shop = ' . $idShop . '
                GROUP BY p.id_product
                ORDER BY total_views DESC
                LIMIT ' . (int)$limit;

        $result = $this->db->executeS($sql);

        if (!$result) {
            return $this->getFallbackMostViewed($limit);
        }

        return $result;
    }

    private function getFallbackMostViewed($limit = 5)
    {
        $idLang = (int)Context::getContext()->language->id;
        $idShop = (int)Context::getContext()->shop->id;

        $sql = 'SELECT p.id_product, pl.name, 0 as total_views
                FROM ' . _DB_PREFIX_ . 'product p
                JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . '
                JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product AND ps.id_shop = ' . $idShop . '
                WHERE p.active = 1
                ORDER BY p.date_add DESC
                LIMIT ' . (int)$limit;

        return $this->db->executeS($sql);
    }

    public function getLowStockAlerts($threshold)
    {
        $idLang = (int)Context::getContext()->language->id;
        $idShop = (int)Context::getContext()->shop->id;

        $sql = 'SELECT p.id_product, pl.name, sa.quantity as physical_quantity
                FROM ' . _DB_PREFIX_ . 'product p
                JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . '
                JOIN ' . _DB_PREFIX_ . 'stock_available sa ON sa.id_product = p.id_product
                JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product AND ps.id_shop = ' . $idShop . '
                WHERE sa.quantity <= ' . (int)$threshold . '
                AND sa.id_product_attribute = 0
                AND sa.id_shop = ' . $idShop . '
                AND p.active = 1
                ORDER BY sa.quantity ASC, pl.name ASC
                LIMIT 10';

        $result = $this->db->executeS($sql);

        return $result ? $result : [];
    }

    public function getProductEditLink($idProduct)
    {
        $link = Context::getContext()->link;
        return $link->getAdminLink('AdminProducts', true, [], ['id_product' => $idProduct, 'updateproduct' => 1]);
    }
}
