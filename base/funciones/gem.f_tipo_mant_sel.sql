CREATE OR REPLACE FUNCTION gem.f_tipo_mant_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.f_tipo_mant_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.ttipo_mant'
 AUTOR: 		 (admin)
 FECHA:	        17-08-2012 12:04:42
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:	
 AUTOR:			
 FECHA:		
***************************************************************************/

DECLARE

	v_consulta    		varchar;
	v_parametros  		record;
	v_nombre_funcion   	text;
	v_resp				varchar;
			    
BEGIN

	v_nombre_funcion = 'gem.f_tipo_mant_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_GETIMA_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		17-08-2012 12:04:42
	***********************************/

	if(p_transaccion='GEM_GETIMA_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						getima.id_tipo_mant,
						getima.codigo,
						getima.nombre,
						getima.estado_reg,
						getima.id_usuario_reg,
						getima.fecha_reg,
						getima.id_usuario_mod,
						getima.fecha_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						getima.tipo	
						from gem.ttipo_mant getima
						inner join segu.tusuario usu1 on usu1.id_usuario = getima.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = getima.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GETIMA_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		17-08-2012 12:04:42
	***********************************/

	elsif(p_transaccion='GEM_GETIMA_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_tipo_mant)
					    from gem.ttipo_mant getima
					    inner join segu.tusuario usu1 on usu1.id_usuario = getima.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = getima.id_usuario_mod
					    where ';
			
			--Definicion de la respuesta		    
			v_consulta:=v_consulta||v_parametros.filtro;

			--Devuelve la respuesta
			return v_consulta;

		end;
					
	else
					     
		raise exception 'Transaccion inexistente';
					         
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