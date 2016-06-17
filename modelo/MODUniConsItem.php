<?php
/**
*@package       pXP
*@file          gen-MODUniConsItem.php
*@author        (rac)
*@date          01-11-2012 11:53:15
*@description   Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
*/

class MODUniConsItem extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarUniConsItem(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='gem.ft_uni_cons_item_sel';
		$this->transaccion='GEM_UNITEM_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		
		$this->setParametro('id_uni_cons','id_uni_cons','int4');				
		//Definicion de la lista del resultado del query
		$this->captura('id_uni_cons_item','int4');
		$this->captura('estado_reg','varchar');
		$this->captura('id_uni_cons','int4');
		$this->captura('id_item','int4');
        $this->captura('nombre','varchar');
        $this->captura('observaciones','varchar');
        $this->captura('codigo','varchar');		
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('id_usuario_mod','int4');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('id_proveedor','int4');
		$this->captura('desc_proveedor','varchar');
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		//echo $this->consulta;exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarUniConsItem(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='gem.ft_uni_cons_item_ime';
		$this->transaccion='GEM_UNITEM_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_uni_cons','id_uni_cons','int4');
		$this->setParametro('id_item','id_item','int4');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_proveedor','id_proveedor','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarUniConsItem(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='gem.ft_uni_cons_item_ime';
		$this->transaccion='GEM_UNITEM_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_uni_cons_item','id_uni_cons_item','int4');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('id_uni_cons','id_uni_cons','int4');
		$this->setParametro('id_item','id_item','int4');
		$this->setParametro('observaciones','observaciones','varchar');
		$this->setParametro('id_proveedor','id_proveedor','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarUniConsItem(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='gem.ft_uni_cons_item_ime';
		$this->transaccion='GEM_UNITEM_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_uni_cons_item','id_uni_cons_item','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	
	function listarItemProveedor(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='gem.ft_uni_cons_item_sel';
		$this->transaccion='GEM_ITEPRO_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
		$this-> setCount(false);
		
			
		//Definicion de la lista del resultado del query
		$this->captura('nombre','varchar');
		$this->captura('codigo','varchar');
		$this->captura('desc_proveedor','varchar');
		$this->captura('contacto','text');
        $this->captura('direccion','varchar');
        $this->captura('telefono1','varchar');
        $this->captura('email1','varchar');		
		
		//Ejecuta la instruccion
		$this->armarConsulta();
		//echo $this->consulta;exit;
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
}
?>