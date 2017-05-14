CREATE OR REPLACE FUNCTION "gem"."ft_uni_cons_eventos_sel"(	
				p_administrador integer, p_id_usuario integer, p_tabla character varying, p_transaccion character varying)
RETURNS character varying AS
$BODY$
/**************************************************************************
 SISTEMA:		Sistema de Mantenimiento Industrial
 FUNCION: 		gem.ft_uni_cons_eventos_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.tuni_cons_eventos'
 AUTOR: 		 (admin)
 FECHA:	        04-05-2017 02:46:39
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

	v_nombre_funcion = 'gem.ft_uni_cons_eventos_sel';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************    
 	#TRANSACCION:  'GM_UCOEVE_SEL'
 	#DESCRIPCION:	Consulta de datos
 	#AUTOR:		admin	
 	#FECHA:		04-05-2017 02:46:39
	***********************************/

	if(p_transaccion='GM_UCOEVE_SEL')then
     				
    	begin
    		--Sentencia de la consulta
			v_consulta:='select
						ucoeve.id,
						ucoeve.id_equipo_medicion,
						ucoeve.tipo,
						ucoeve.atributos,
						ucoeve.estado_reg,
						ucoeve.geofenceid,
						ucoeve.servertime,
						ucoeve.id_usuario_reg,
						ucoeve.usuario_ai,
						ucoeve.fecha_reg,
						ucoeve.id_usuario_ai,
						ucoeve.fecha_mod,
						ucoeve.id_usuario_mod,
						usu1.cuenta as usr_reg,
						usu2.cuenta as usr_mod	
						from gem.tuni_cons_eventos ucoeve
						inner join segu.tusuario usu1 on usu1.id_usuario = ucoeve.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ucoeve.id_usuario_mod
				        where  ';
			
			--Definicion de la respuesta
			v_consulta:=v_consulta||v_parametros.filtro;
			v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

			--Devuelve la respuesta
			return v_consulta;
						
		end;

	/*********************************    
 	#TRANSACCION:  'GM_UCOEVE_CONT'
 	#DESCRIPCION:	Conteo de registros
 	#AUTOR:		admin	
 	#FECHA:		04-05-2017 02:46:39
	***********************************/

	elsif(p_transaccion='GM_UCOEVE_CONT')then

		begin
			--Sentencia de la consulta de conteo de registros
			v_consulta:='select count(id)
					    from gem.tuni_cons_eventos ucoeve
					    inner join segu.tusuario usu1 on usu1.id_usuario = ucoeve.id_usuario_reg
						left join segu.tusuario usu2 on usu2.id_usuario = ucoeve.id_usuario_mod
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
$BODY$
LANGUAGE 'plpgsql' VOLATILE
COST 100;
ALTER FUNCTION "gem"."ft_uni_cons_eventos_sel"(integer, integer, character varying, character varying) OWNER TO postgres;
