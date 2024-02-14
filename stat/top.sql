select
  concat(name, ' ', surname) as name,
  a as average_score,
  n as stakes_count
from (
  select
    s / n as a,
    n,
    uid
  from (
    select 
      uid,
      count(*) as n,
      sum(score) as s
    from 
      totalizator.total_stakes st
      join totalizator.total_matches m
      on st.match_id = m.id
    where m.comp_id >=10 and st.played = 1
    group by uid
  ) t 
  where n > 1000 order by s/n desc
) stat 
join pipeinpipe.p_user
on stat.uid = id;
