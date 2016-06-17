--------------- SQL ---------------

CREATE OR REPLACE FUNCTION gem.ft_funcion_falla_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION: 		gem.ft_funcion_falla_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.tfuncion_falla'
 AUTOR: 		 (admin)
 FECHA:	        30-09-2012 21:41:13
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

	v_nombre_funcion = 'gem.ft_funcion_falla_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GEM_GEFALL_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:41:13
	***********************************/

	if(p_transaccion='GEM_GEFALL_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						gefall.id_funcion_falla,
						gefall.id_funcion,
						gefall.id_falla_evento,
						gefall.modo_falla,
						gefall.orden,
						gefall.efecto_falla,
						gefall.estado_reg,
						gefall.fecha_reg,
						gefall.id_usuario_reg,
						gefall.fecha_mod,
						gefall.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod,
						gefaev.nombre as desc_falla_evento,
						gefall.falla
						from gem.tfuncion_falla gefall
						inner join segu.tusuario usu1 on usu1.id_usuario = gefall.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = gefall.id_usuario_mod
						left join gem.tfalla_evento gefaev on gefaev.id_falla_evento = gefall.id_falla_evento
				    where ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'GEM_GEFALL_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		30-09-2012 21:41:13
	***********************************/

	elsif(p_transaccion='GEM_GEFALL_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id_funcion_falla)
					    from gem.tfuncion_falla gefall
						inner join segu.tusuario usu1 on usu1.id_usuario = gefall.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = gefall.id_usuario_mod
						left join gem.tfalla_evento gefaev on gefaev.id_falla_evento = gefall.id_falla_evento
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