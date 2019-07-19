排序

locLongitude--当前经度，
locLatitude--当前纬度；

longitude -- 数据库经度字段名 
latitude --数据库纬度字段名

传入当前坐标，查询数据库中距离最近的两个单位。

select * from t_depot order by ACOS(SIN((#{locLatitude} * 3.1415) / 180 ) *SIN((latitude * 3.1415) / 180 ) 
+COS((#{locLatitude} * 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS((#{locLongitude} * 3.1415) / 180
- (longitude * 3.1415) / 180 ) ) * 6380  asc  limit 2;


指定范围 
经度:113.914619 
纬度:22.50128 
范围:2km 
longitude为数据表经度字段 
latitude为数据表纬度字段 
SQL在mysql下测试通过,其他数据库可能需要修改 
SQL语句如下: 
select * from location where sqrt( ( ((113.914619-longitude)*PI()*12656*cos(((22.50128+latitude)/2)*PI()/180)/180) 
* ((113.914619-longitude)*PI()*12656*cos (((22.50128+latitude)/2)*PI()/180)/180) ) + ( ((22.50128-latitude)*PI()*12656/180) 
* ((22.50128-latitude)*PI()*12656/180) ) )<2

