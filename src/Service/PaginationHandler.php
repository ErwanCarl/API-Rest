<?php

namespace App\Service;

use Exception;
use JMS\Serializer\Serializer;

class PaginationHandler {

    public function isPhonePageEmpty(int $phones, int $numberOfObject, int $page, int $limit) 
    {
        $phoneStartNumber = ($page - 1) * $limit + 1;
        $totalPages = ceil($numberOfObject / $limit);

        if($phones === 0) {
            throw new Exception("La page ".$page." est vide. Il y a au total ".$numberOfObject." téléphones et un total de ".$totalPages." page(s) avec cette valeur de limit(".$limit."). Avec ces valeurs de paramètres utilisés pour page(".$page.") et limit(".$limit.") dans l'url, vous demandez d'afficher les téléphones à partir du ".$phoneStartNumber."° de la liste, qui n'existent pas.", 666);
        }    
    }

    public function isCustomerPageEmpty(int $customers, int $numberOfObject, int $page, int $limit) 
    {
        $customerStartNumber = ($page - 1) * $limit + 1;
        $totalPages = ceil($numberOfObject / $limit);

        if($customers === 0) {
            throw new Exception("La page ".$page." est vide. Il y a au total ".$numberOfObject." clients liés à votre username (i.e l'identifiant de votre Marketplace) et un total de ".$totalPages." page(s) avec cette valeur de limit(".$limit."). Avec ces valeurs de paramètres utilisés pour page(".$page.") et limit(".$limit.") dans l'url, vous demandez d'afficher les clients à partir du ".$customerStartNumber."° de la liste, qui n'existent pas.", 999);
        }    
    }
}
