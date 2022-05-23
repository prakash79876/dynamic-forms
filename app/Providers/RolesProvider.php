<?php
namespace jazmy\FormBuilder\Services;
class RolesProvider
{
    public function __invoke() : array
    {
    	return [
    		1 => 'Admin',
    		2 => 'User',
    	];
    }
}
