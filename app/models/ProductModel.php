<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Model: ProductModel
 */
class ProductModel extends Model {
    protected $table = 'products';
    protected $primary_key = 'id';

    protected $fillable = [
        'product_name',
        'description',
        'price',
        'quantity'
    ];

    protected $guarded = ['id'];

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_products()
    {
        return $this->db->table($this->table)->get_all();
    }

    public function get_product($id)
    {
        return $this->db->table($this->table)
                        ->where('id', $id)
                        ->get();
    }

    public function add_product($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    public function update_product($id, $data)
    {
        return $this->db->table($this->table)
                        ->where('id', $id)
                        ->update($data);
    }

    public function delete_product($id)
    {
        return $this->db->table($this->table)
                        ->where('id', $id)
                        ->delete();
    }
}