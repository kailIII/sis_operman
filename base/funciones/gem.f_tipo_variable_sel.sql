CREATE OR REPLACE FUNCTION gem.f_tipo_variable_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:   SISTEMA DE GESTION DE MANTENIMIENTO
 FUNCION:     gem.f_tipo_variable_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'gem.ttipo_variable'
 AUTOR:      (rac)
 FECHA:         15-08-2012 15:28:09
 COMENTARIOS: 
***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION: 
 AUTOR:     
 FECHA:   
***************************************************************************/

DECLARE

  v_consulta        varchar;
  v_parametros      record;
  v_nombre_funcion    text;
  v_resp        varchar;
          
BEGIN

  v_nombre_funcion = 'gem.f_tipo_variable_sel';
    v_parametros = pxp.f_get_record(p_tabla);

  /*********************************    
  #TRANSACCION:  'GEM_TVA_SEL'
  #DESCRIPCION: Consulta de datos
  #AUTOR:   rac 
  #FECHA:   15-08-2012 15:28:09
  ***********************************/

  if(p_transaccion='GEM_TVA_SEL')then
            
      begin
        
        
            IF (not pxp.f_existe_parametro(p_tabla,'id_tipo_equipo')  or v_parametros.id_tipo_equipo is null) THEN
                   raise exception 'El tipo de Equipo no ha sido definido';
        
            END IF;
           
        
        
        --Sentencia de la consulta
      v_consulta:='select
            tva.id_tipo_variable,
            tva.estado_reg,
            tva.nombre,
            tva.id_tipo_equipo,
            tva.id_unidad_medida,
            tva.descripcion,
            tva.id_usuario_reg,
            tva.fecha_reg,
            tva.id_usuario_mod,
            tva.fecha_mod,
            usu1.cuenta as usr_reg,
            usu2.cuenta as usr_mod,
            um.codigo as codigo_unidad_medida,
            tva.observaciones,
            tva.orden 
            from gem.ttipo_variable tva
            inner join segu.tusuario usu1 on usu1.id_usuario = tva.id_usuario_reg
            left join param.tunidad_medida um on  um.id_unidad_medida = tva.id_unidad_medida
            left join segu.tusuario usu2 on usu2.id_usuario = tva.id_usuario_mod
            where  tva.estado_reg = ''activo''  AND tva.id_tipo_equipo ='|| v_parametros.id_tipo_equipo||' AND ';
      
      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      --Devuelve la respuesta
      return v_consulta;
            
    end;

  /*********************************    
  #TRANSACCION:  'GEM_TVA_CONT'
  #DESCRIPCION: Conteo de registros
  #AUTOR:   rac 
  #FECHA:   15-08-2012 15:28:09
  ***********************************/

  elsif(p_transaccion='GEM_TVA_CONT')then

    begin
        
        
             IF (not pxp.f_existe_parametro(p_tabla,'id_tipo_equipo')  or v_parametros.id_tipo_equipo is null) THEN
                   raise exception 'El tipo de Equipo no ha sido definido';
        
            END IF;
            
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(id_tipo_variable)
             from gem.ttipo_variable tva
            inner join segu.tusuario usu1 on usu1.id_usuario = tva.id_usuario_reg
                        left join param.tunidad_medida um on  um.id_unidad_medida = tva.id_unidad_medida
            left join segu.tusuario usu2 on usu2.id_usuario = tva.id_usuario_mod
                where  tva.estado_reg = ''activo''  AND tva.id_tipo_equipo ='|| v_parametros.id_tipo_equipo||' AND ';
      
      
      --Definicion de la respuesta        
      v_consulta:=v_consulta||v_parametros.filtro;

      --Devuelve la respuesta
      return v_consulta;

    end;
        
    /*********************************    
  #TRANSACCION:  'GEM_TODVAR_SEL'
  #DESCRIPCION: Listado de todas las variables por id_tipo_equipo
  #AUTOR:     rcm
  #FECHA:     22/03/2013
  ***********************************/

  elsif(p_transaccion='GEM_TODVAR_SEL')then
            
      begin
        
            IF (not pxp.f_existe_parametro(p_tabla,'id_tipo_equipo')) THEN
                   raise exception 'El tipo de Equipo no ha sido definido';
            END IF;

        --Sentencia de la consulta
      v_consulta:='select
                        distinct tvar.id_tipo_variable::varchar as id, tvar.nombre
                        from gem.tequipo_variable evar
                        inner join gem.ttipo_variable tvar
                        on tvar.id_tipo_variable = evar.id_tipo_variable
                        inner join gem.tuni_cons ucons
                        on ucons.id_uni_cons = evar.id_uni_cons
                        WHERE ';
      
      --Definicion de la respuesta
      v_consulta:=v_consulta||v_parametros.filtro;
      v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;

      --Devuelve la respuesta
      return v_consulta;
            
    end;
        
    /*********************************    
  #TRANSACCION:  'GEM_TODVAR_CONT'
  #DESCRIPCION: Conteo de registros
  #AUTOR:     rcm
  #FECHA:     22/03/2013
  ***********************************/

  elsif(p_transaccion='GEM_TODVAR_CONT')then

    begin
        
      IF (not pxp.f_existe_parametro(p_tabla,'id_tipo_equipo')  or v_parametros.id_tipo_equipo is null) THEN
              raise exception 'El tipo de Equipo no ha sido definido';
            END IF;
            
      --Sentencia de la consulta de conteo de registros
      v_consulta:='select count(distinct tvar.id_tipo_variable)
              from gem.tequipo_variable evar
                        inner join gem.ttipo_variable tvar
                        on tvar.id_tipo_variable = evar.id_tipo_variable
                        inner join gem.tuni_cons ucons
                        on ucons.id_uni_cons = evar.id_uni_cons
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