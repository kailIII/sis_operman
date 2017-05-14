CREATE OR REPLACE FUNCTION gem.ft_funcion_analisis_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.ft_funcion_analisis_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'gem.tfuncion'
 AUTOR: 		 (admin)
 FECHA:	        30-09-2012 21:41:09
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_funcion	integer;
			    
BEGIN

    v_nombre_funcion = 'gem.ft_funcion_analisis_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_GEFUNC_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:41:09
	***********************************/

	if(p_transaccion='GEM_GEFUNC_INS')then
					
        begin
        	--Sentencia de la insercion
        	insert into gem.tfuncion(
			id_analisis_mant,
			descripcion,
			orden,
			estado_reg,
			id_usuario_reg,
			fecha_reg,
			fecha_mod,
			id_usuario_mod
          	) values(
			v_parametros.id_analisis_mant,
			v_parametros.descripcion,
			v_parametros.orden,
			'activo',
			p_id_usuario,
			now(),
			null,
			null
			)RETURNING id_funcion into v_id_funcion;
               
			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Funciones almacenado(a) con exito (id_funcion'||v_id_funcion||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_funcion',v_id_funcion::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GEFUNC_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:41:09
	***********************************/

	elsif(p_transaccion='GEM_GEFUNC_MOD')then

		begin
			--Sentencia de la modificacion
			update gem.tfuncion set
			id_analisis_mant = v_parametros.id_analisis_mant,
			descripcion = v_parametros.descripcion,
			orden = v_parametros.orden,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario
			where id_funcion=v_parametros.id_funcion;
               
			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Funciones modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_funcion',v_parametros.id_funcion::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GEFUNC_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:41:09
	***********************************/

	elsif(p_transaccion='GEM_GEFUNC_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from gem.tfuncion
            where id_funcion=v_parametros.id_funcion;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Funciones eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_funcion',v_parametros.id_funcion::varchar);
              
            --Devuelve la respuesta
            return v_resp;

		end;
         
	else
     
    	raise exception 'Transaccion inexistente: %',p_transaccion;

	end if;

EXCEPTION
				
	WHEN OTHERS THEN
		v_resp='';
		v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
		v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
		v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
		raise exception '%',v_resp;
				        
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;