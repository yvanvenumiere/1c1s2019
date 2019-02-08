<?php
namespace AlectisLab\UtilsBundle\Listener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent; 
class AllPagesListener
{	 
    public function onPageDisplay(FilterControllerEvent $event) 	
    {
        // Récupération de l'event
        if(HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) 
        {
            // Récupération du controller    
            $_controller = $event->getController();
            //var_dump($event->getController());exit;
            if (isset($_controller[0])) 
            {
                $controller = $_controller[0];
                // On vérifie que le controller implémente la méthode preExecute
                if(method_exists($controller,'preExecute'))
                {
                    $controller->preExecute();
                }
            }
        }
 
    }
}