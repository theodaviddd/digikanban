<?php

/**
 * Tool class for module advanced kanban
 * use class with statics methods to easiest copy to another module
 * like a lib but with a "kind of workspace"
 */
class AdvKanbanTools{



	/**
	 * Create a new object instance based on the element type
	 * Fetch the object if id is provided
	 *
	 * @param string $elementType Type of object ('invoice', 'order', 'expedition_bon', 'myobject@mymodule', ...)
	 * @param int    $elementId   Id of element to provide if fetch is needed
	 * @param int    $maxCacheByType max number of cached element by type
	 * @return CommonObject object of $elementType, fetched by $elementId
	 */
	static function getObjectByElement($elementType, $elementId = false, $maxCacheByType = 10)
	{
		global $conf, $db, $hookmanager;

		$regs = array();

		// Parse $objecttype (ex: project_task)
		$module = $myObject = $elementType;

		// If we ask an resource form external module (instead of default path)
		if (preg_match('/^([^@]+)@([^@]+)$/i', $elementType, $regs)) {
			$myObject = $regs[1];
			$module = $regs[2];
		}


		if (preg_match('/^([^_]+)_([^_]+)/i', $elementType, $regs))
		{
			$module = $regs[1];
			$myObject = $regs[2];
		}

		// Generic case for $classpath
		$classpath = $module.'/class';

		// Special cases, to work with non standard path
		if ($elementType == 'facture' || $elementType == 'invoice') {
			$classpath = 'compta/facture/class';
			$module='facture';
			$myObject='facture';
		}
		elseif ($elementType == 'commande' || $elementType == 'order') {
			$classpath = 'commande/class';
			$module='commande';
			$myObject='commande';
		}
		elseif ($elementType == 'contact')  {
			$module = 'societe';
		}
		elseif ($elementType == 'propal')  {
			$classpath = 'comm/propal/class';
		}
		elseif ($elementType == 'shipping') {
			$classpath = 'expedition/class';
			$myObject = 'expedition';
			$module = 'expedition';
		}
		elseif ($elementType == 'delivery') {
			$classpath = 'delivery/class';
			$myObject = 'delivery';
			$module = 'expedition';
		}
		elseif ($elementType == 'contract') {
			$classpath = 'contrat/class';
			$module='contrat';
			$myObject='contrat';
		}
		elseif ($elementType == 'member') {
			$classpath = 'adherents/class';
			$module='adherent';
			$myObject='adherent';
		}
		elseif ($elementType == 'cabinetmed_cons') {
			$classpath = 'cabinetmed/class';
			$module='cabinetmed';
			$myObject='cabinetmedcons';
		}
		elseif ($elementType == 'fichinter') {
			$classpath = 'fichinter/class';
			$module='ficheinter';
			$myObject='fichinter';
		}
		elseif ($elementType == 'task') {
			$classpath = 'projet/class';
			$module='projet';
			$myObject='task';
		}
		elseif ($elementType == 'stock') {
			$classpath = 'product/stock/class';
			$module='stock';
			$myObject='stock';
		}
		elseif ($elementType == 'inventory') {
			$classpath = 'product/inventory/class';
			$module='stock';
			$myObject='inventory';
		}
		elseif ($elementType == 'mo') {
			$classpath = 'mrp/class';
			$module='mrp';
			$myObject='mo';
		}
		elseif ($elementType == 'salary') {
			$classpath = 'salaries/class';
			$module='salaries';
		}
		elseif ($elementType == 'chargesociales') {
			$classpath = 'compta/sociales/class';
			$module='tax';
		}
		elseif ($elementType == 'tva') {
			$classpath = 'compta/tva/class';
			$module='tax';
		}
		elseif ($elementType == 'widthdraw') {
			$classpath = 'compta/prelevement/class';
			$module='prelevement';
			$myObject='bonprelevement';
		}
		elseif ($elementType == 'project') {
			$classpath = 'projet/class';
			$module='projet';
		}
		elseif ($elementType == 'project_task') {
			$classpath = 'projet/class';
			$module='projet';
		}
		elseif ($elementType == 'action') {
			$classpath = 'comm/action/class';
			$module='agenda';
			$myObject = 'ActionComm';
		}
		elseif ($elementType == 'mailing') {
			$classpath = 'comm/mailing/class';
		}
		elseif ($elementType == 'knowledgerecord') {
			$classpath = 'knowledgemanagement/class';
			$module='knowledgemanagement';
		}
		elseif ($elementType == 'recruitmentjobposition') {
			$classpath = 'recruitment/class';
			$module='recruitment';
		}
		elseif ($elementType == 'recruitmentcandidature') {
			$classpath = 'recruitment/class';
			$module='recruitment';
		}

		// Generic case for $classfile and $classname
		$classfile = strtolower($myObject); $classname = ucfirst($myObject);
		//print "objecttype=".$objecttype." module=".$module." subelement=".$subelement." classfile=".$classfile." classname=".$classname;

		if ($elementType == 'invoice_supplier') {
			$classfile = 'fournisseur.facture';
			$classname = 'FactureFournisseur';
			$classpath = 'fourn/class';
			$module = 'fournisseur';
		}
		elseif ($elementType == 'order_supplier') {
			$classfile = 'fournisseur.commande';
			$classname = 'CommandeFournisseur';
			$classpath = 'fourn/class';
			$module = 'fournisseur';
		}
		elseif ($elementType == 'supplier_proposal')  {
			$classfile = 'supplier_proposal';
			$classname = 'SupplierProposal';
			$classpath = 'supplier_proposal/class';
			$module = 'supplier_proposal';
		}
		elseif ($elementType == 'stock') {
			$classpath = 'product/stock/class';
			$classfile = 'entrepot';
			$classname = 'Entrepot';
		}
		elseif ($elementType == 'dolresource') {
			$classpath = 'resource/class';
			$classfile = 'dolresource';
			$classname = 'Dolresource';
			$module = 'resource';
		}
		elseif ($elementType == 'payment_various') {
			$classpath = 'compta/bank/class';
			$module='tax';
			$classfile = 'paymentvarious';
			$classname = 'PaymentVarious';
		}
		elseif ($elementType == 'bank_account') {
			$classpath = 'compta/bank/class';
			$module='banque';
			$classfile = 'account';
			$classname = 'Account';
		}
		elseif ($elementType == 'adherent_type')  {
			$classpath = 'adherents/class';
			$module = 'member';
			$classfile='adherent_type';
			$classname='AdherentType';
		}

        // Add  hook
        if (!is_object($hookmanager)) {
            include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
            $hookmanager = new HookManager($db);
        }
        $hookmanager->initHooks(array('kanbanTool'));


        // Hook params
        $parameters = array(
            'elementType' =>& $elementType,
            'elementId' =>& $elementId,
            'classpath' =>& $classpath,
            'classfile' =>& $classfile,
            'classname' =>& $classname,
            'module' =>& $module,
            'myObject' =>& $myObject,
        );

        $reshook = $hookmanager->executeHooks('kanban_getObjectByElement', $parameters, $object);
        if ($reshook > 0 && is_object($object)) {
            return $object;
        }

        // context of elementproperties doesn't need to exist out of this function so delete it to avoid elementproperties is equal to all
        if (($key = array_search('kanbanTool', $hookmanager->contextarray)) !== false) {
            unset($hookmanager->contextarray[$key]);
        }

		if (!empty($conf->$module->enabled))
		{
			$res = dol_include_once('/'.$classpath.'/'.$classfile.'.class.php');
			if ($res)
			{
				if (class_exists($classname))
				{
					if($elementId === false){
						return new $classname($db);
					}else{
						return self::getObjectFromCache($classname, $elementId, $maxCacheByType);
					}
				}
			}
		}
		return false;
	}


	/**
	 * @param string $objetClassName
	 * @param int $fk_object
	 * @param int $maxCacheByType
	 * @return bool|CommonObject
	 */
	static function getObjectFromCache($objetClassName, $fk_object, $maxCacheByType = 10){
		global $db, $TAdvKanbanToolsGetObjectFromCache;

		if(!class_exists($objetClassName)){
			// TODO : Add error log here
			return false;
		}



		if(empty($TAdvKanbanToolsGetObjectFromCache[$objetClassName][$fk_object])){
			$object = new $objetClassName($db);
			if($object->fetch($fk_object, false) <= 0)
			{
				return false;
			}

			if(!empty($TAdvKanbanToolsGetObjectFromCache[$objetClassName]) && is_array($TAdvKanbanToolsGetObjectFromCache[$objetClassName]) && count($TAdvKanbanToolsGetObjectFromCache[$objetClassName]) >= $maxCacheByType){
				// les clés sont importantes je veux être sûr de les préserver c'est pourquoi je n'utilise plus array_shift
				foreach ($TAdvKanbanToolsGetObjectFromCache[$objetClassName] as $TCacheKey => $TCacheVal){
					unset($TAdvKanbanToolsGetObjectFromCache[$objetClassName][$TCacheKey]);
					if(count($TAdvKanbanToolsGetObjectFromCache[$objetClassName]) <= $maxCacheByType){ break; }
				}
			}

			$TAdvKanbanToolsGetObjectFromCache[$objetClassName][$fk_object] = $object;
		}
		else{
			$object = $TAdvKanbanToolsGetObjectFromCache[$objetClassName][$fk_object];
		}

		return $object;
	}
}
