SELECT count(*) FROM (

    SELECT 
        ps.id,ps.mysql_formatted_date as mysql_formatted_date,ps.title as title,"press_releases" as dataset,IFNULL(mnlj.name,"") as ministry 
    FROM press_releases ps lEFT JOIN ministry mnlj ON ps.ministry_id=mnlj.id 
    WHERE  
    ps.mysql_formatted_date>=:mysql_formatted_date_ps0 
    AND ps.mysql_formatted_date<=:mysql_formatted_date_ps1 
    AND ( 
        MATCH (mnlj.name) AGAINST (:name_mnljps2  IN BOOLEAN MODE) 
        OR  
        MATCH (ps.content,ps.title) AGAINST (+*:title_ps3*  IN BOOLEAN MODE)
    )

) xxx