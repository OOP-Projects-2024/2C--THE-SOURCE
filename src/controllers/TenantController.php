<?php
require_once __DIR__ . '/../models/Tenant.php';

class TenantController {
    private $tenantModel;

    public function __construct() {
        $this->tenantModel = new Tenant();
    }

    public function createTenant($data) {
        return $this->tenantModel->create(
            $data['name'], 
            $data['email'], 
            $data['contact'], 
            $data['address'], 
            $data['property_id']
        );
    }

    public function updateTenant($id, $data) {
        return $this->tenantModel->update(
            $id,
            $data['name'], 
            $data['email'], 
            $data['contact'], 
            $data['address'], 
            $data['property_id']
        );
    }

    public function deleteTenant($id) {
        return $this->tenantModel->delete($id);
    }

    public function getTenants() {
        return $this->tenantModel->getAll();
    }
    
}
?>

