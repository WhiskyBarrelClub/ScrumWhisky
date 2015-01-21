<?php

namespace scrum\ScotchLodge\Controllers;

use Doctrine\ORM\EntityManager;
use Slim\Slim;
use scrum\ScotchLodge\Controllers\Controller;
use scrum\ScotchLodge\Service\Whisky\WhiskyService;
use scrum\ScotchLodge\Service\Region\RegionService;
use scrum\ScotchLodge\Service\Distillery\DistilleryService;
use scrum\ScotchLodge\Service\Barrel\BarrelService;
use scrum\ScotchLodge\Entities\Whisky;

/**
 * WhiskyController controller
 * 
 * @author joeri broos 
 */
class WhiskyController extends Controller{
    
    /*@var $whiskysrv WhiskyService */
    private $whiskysrv;
    private $em;
    private $app;

    public function __construct($em, $app) {
        parent::__construct($em, $app);
        $this->whiskysrv = new WhiskyService($em, $app);
        $this->em = $em;
        $this->app = $app;
    }
    
    /**
     * Render the page to add a new whisky.
     */
    public function addWhisky(){
        $regsrv = new RegionService($this->em, $this->app);
        $regions = $regsrv->getRegions();
        $distsrv = new DistilleryService($this->em, $this->app);
        $distillerys = $distsrv->getDistillerys();
        $barsrv = new BarrelService($this->em, $this->app);
        $barrels = $barsrv->getBarrels();
        $globals = $this->getGlobals();
        $this->getApp()->render('Whisky/new_whisky.html.twig', array('globals' => $globals, 'regions' => $regions, 'distillerys' => $distillerys, 'barrels' => $barrels));
    }

    public function insertWhisky(){
        try{
            $whisky = $this->whiskysrv->addWhisky();
            if($whisky){
                $url = $this->getApp()->urlFor('new_whisky_ok');
                $this->getApp()->redirect($url);
            }else{
                $errors = $this->whiskysrv->getErrors();
                $regsrv = new RegionService($this->em, $this->app);
                $regions = $regsrv->getRegions();
                $distsrv = new DistilleryService($this->em, $this->app);
                $distillerys = $distsrv->getDistillerys();
                $barsrv = new BarrelService($this->em, $this->app);
                $barrels = $barsrv->getBarrels();
                $globals = $this->getGlobals();
                $this->getApp()->render('Whisky/new_whisky.html.twig', array('globals' => $globals, 'errors' => $errors, 'regions' => $regions, 'distillerys' => $distillerys, 'barrels' => $barrels));
            }
        } catch (Exception $ex) {
            $this->getApp()->render('probleem.twig.html');
        }
    }
    
}