<?php
namespace models;
class data_collected extends dtbase{

   /**
    * Une variable dans laquel on stocke le nom de la table ou les informations sont recupérées =>Ce qui rend le code beaucoup polus flexible et dynamique
    */
   public string $data_type_name; 


   /**
    * @constructor
    */
   public function __construct(
      string $data_type_valid_name
   )

   {
      /**
       * initialisation de la valeur du constructeur 
       */
      $this->data_type_name = $data_type_valid_name;
   }
    
function GetAllData(){
   $dta= $this->GetManyData("SELECT* FROM `vdata`.$this->data_type_name");
   return $dta;
}

function Insert(array $data){
   $this->SendData("INSERT INTO $this->data_type_name (`formulaire`, `adset_name`, `campagne`, `leadgen_id`, `fai_actuel`, `fai_actuel_val`, `code_postal`, `email`, `telephone`, `retour_api`, `code_retour_api`, `created_at`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",$data);
}

function Update(array $data){
   $this->SendData("UPDATE $this->data_type_name SET (formulaire=?,adset_name=?,campagne=?,leadgen_id=?,fai_actuel=?,fai_actuel_val=?,code_postal=?,email=?,telephone=?,retour_api=?,code_retour_api=?,created_at=?)WHERE id=?",$data);
}
function Delete(int $id){
   $this->SendData("DELETE  FROM $this->data_type_name WHERE id=?",[$id]);
}
function GetData(): array{
   return $this->GetManyData("SELECT `formulaire`, `adset_name`, `campagne`, `leadgen_id`, `fai_actuel`, `fai_actuel_val`, `code_postal`, `email`, `telephone`, `retour_api`, `code_retour_api`, `created_at FROM $this->data_type_name,null");
}
function GetSearchData(array $data): array{
   return $this->GetManyData("SELECT * FROM $this->data_type_name WHERE `campagne`='FIBRE' AND `created_at` BETWEEN ? AND ?",$data);
}
function GetCount(array $data){
   return $this->GetOneData("SELECT COUNT(`id`) as total FROM $this->data_type_name WHERE `campagne`='FIBRE' AND `created_at` BETWEEN ? AND ?",$data);
}

function TempData(array $data): array{
   return $this->GetManyData("SELECT `formulaire`, `adset_name`, `campagne`, `leadgen_id`, `fai_actuel`, `fai_actuel_val`, `code_postal`, `email`, `telephone`, `retour_api`, `code_retour_api`, `created_at FROM $this->data_type_name WHERE created_at=?",$data);
}
function TotalNixxis(array $data){
   return $this->GetOneData("SELECT COUNT(`id`) as total FROM $this->data_type_name WHERE `campagne`='FIBRE' AND `code_retour_api`=200 AND `created_at` BETWEEN ? AND ?",$data);
}


function TotalDoublon(array $data){
   return $this->GetOneData("SELECT COUNT(`id`) as total FROM $this->data_type_name WHERE `campagne`='FIBRE' AND `code_retour_api`=409 AND `created_at` BETWEEN ? AND ?",$data);
}


function RejetErreur(array $data){
   return $this->GetOneData("SELECT COUNT(`id`) as total FROM $this->data_type_name WHERE `campagne`='FIBRE' AND `code_retour_api`=500 AND `created_at` BETWEEN ? AND ?",$data);
}

function Telephone(array $data){
   return $this-> GetOneData("SELECT COUNT( DISTINCT `telephone`) FROM $this->data_type_name  WHERE `campagne`='FIBRE' AND `created_at` BETWEEN ? AND ?",$data);
}

function exportBasequo(array $data){
   $data= $this->GetManyData("SELECT* FROM `vdata`.$this->data_type_name WHERE `campagne`='FIBRE' AND `created_at` BETWEEN ? AND ?",$data);
   return $data;
}
}
