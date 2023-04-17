<?php

namespace controller;

session_start();
class dashboard
{
    private $model;
    public $data;
    public $data_name;
    use crypt;

    function __construct()
    {
        if(isset($_GET['data_type'])){
            $this->data_name = $this->datadecrypt($_GET['data_type']);
            $this->model = new \models\data_collected($this->data_name);
        }else{
            $this->model = new \models\data_collected('data_collected');
        }
        

        if (isset($_GET['target'])) {
            $target = $this->datadecrypt($_GET['target']);
            if ($this->$target()) {
                $this->$target();
            }
        } else {
            $this->dashboard();
        }
    }

    function dashboard()
    {
        if (isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
            $count = $this->model->GetCount([
                $_POST['date_debut'],
                $_POST['date_fin'],
            ]);
            $nixx = $this->model->TotalNixxis([
                $_POST['date_debut'],
                $_POST['date_fin'],
            ]);
            $dbl = $this->model->TotalDoublon([
                $_POST['date_debut'],
                $_POST['date_fin'],
            ]);
            $err = $this->model->RejetErreur([
                $_POST['date_debut'],
                $_POST['date_fin'],
            ]);
            $tel = $this->model->Telephone([
                $_POST['date_debut'],
                $_POST['date_fin'],
            ]);
            $dblfacebook =
                $count['total'] - $tel['COUNT( DISTINCT `telephone`)'];
            $dblNixxis = $dbl['total'] - $dblfacebook;
            $pourcentageNixxis = ($nixx['total'] / $count['total']) * 100;
            $pourcentageDoublonNixxis = ($dblNixxis / $count['total']) * 100;
            $pourcentageDoublonFacebook =
                ($dblfacebook / $count['total']) * 100;
            $pourcentageErreurIntegration =
                ($err['total'] / $count['total']) * 100;
            $adminUsername = $_SESSION['auth'];
            /****partie ajouter @marcos*/
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            /****partie ajouter @marcos*/
        } else {
            date_default_timezone_set('Europe/London');
            $dateDebut = Date('Y-m-01 00:00:00',time());
            $dateFin = Date('Y-m-d H:i:s',time());
            $count = $this->model->GetCount([$dateDebut, $dateFin]);
            $nixx = $this->model->TotalNixxis([$dateDebut, $dateFin]);
            $dbl = $this->model->TotalDoublon([$dateDebut, $dateFin]);
            $err = $this->model->RejetErreur([$dateDebut, $dateFin]);
            $tel = $this->model->Telephone([$dateDebut, $dateFin]);
            $dblfacebook =
                $count['total'] - $tel['COUNT( DISTINCT `telephone`)'];
            $dblNixxis = $dbl['total'] - $dblfacebook;
            $pourcentageNixxis = ($nixx['total'] / $count['total']) * 100;
            $pourcentageDoublonNixxis = ($dblNixxis / $count['total']) * 100;
            $pourcentageDoublonFacebook =
                ($dblfacebook / $count['total']) * 100;
            $pourcentageErreurIntegration =
                ($err['total'] / $count['total']) * 100;
            $adminUsername = $_SESSION['auth'];
         
        }

        include_once 'views/main.phtml';
    }

    function table()
    {
        $adminUsername = $_SESSION['auth'];

        $data = $this->model->GetAllData();

        include_once 'views/data-table.phtml';
    }

    function disconnect()
    {
        session_start();
        session_destroy();
        header(
            'location:index.php?goto=' .
                $this->datacrypt('login') .
                '&target=' .
                $this->datacrypt('login')
        );
        exit();
    }
    function select()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['valider'])) {
            if (isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
                $data = $this->model->GetSearchData([
                    $_POST['date_debut'],
                    $_POST['date_fin'],
                ]);
                $adminUsername = $_SESSION['auth'];
                /****partie ajouter @marcos*/
                $date_debut = $_POST['date_debut'];
                $date_fin = $_POST['date_fin'];
                /****partie ajouter @marcos*/
                include_once 'views/data-table.phtml';
            }
        } elseif (
            $_SERVER['REQUEST_METHOD'] == 'POST' &&
            isset($_POST['export'])
        ) {
            // echo "yes";
            $date = [$_POST['date_debut'], $_POST['date_fin']];
            $basquo_ok = $this->model->exportBasequo($date);
            $data = $this->model->GetSearchData([
                $_POST['date_debut'],
                $_POST['date_fin'],
            ]);
            $adminUsername = $_SESSION['auth'];

            if ($basquo_ok) {
                $name =
                    'DATA_VIPP_VDATA' .
                    date('Ymd') .
                    '' .
                    date('YmdHis') .
                    '.csv';

                //header csv file
                $header_csv = [
                    [
                        'id' => 'id',
                        'formulaire' => 'formulaire',
                        'adset_name' => 'adset_name',
                        'campagne' => 'campagne',
                        'leadgen_id' => 'leadgen_id',
                        'fai_actuel' => 'fai_actuel',
                        'fai_actuel_val' => 'fai_actuel_val',
                        'code_postal' => 'code_postal',
                        'email' => 'email',
                        'telephone' => 'telephone',
                        'retour_api' => 'retour_api',
                        'code_retour_api' => 'code_retour_api',
                        'callback_time' => 'callback_time',
                        'status' => 'status',
                        'created_at' => 'created_at',
                    ],
                ];

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . $name);
                header('Pragma: no-cache');
                header('Expires: 0');

                $outstream = fopen('php://output', 'wb');

                foreach ($header_csv as $head) {
                    fputcsv($outstream, $head, ';');
                }

                foreach ($basquo_ok as $result) {
                    fputcsv($outstream, $result, ';');
                }

                fclose($outstream);
            }
        }
    }
}
