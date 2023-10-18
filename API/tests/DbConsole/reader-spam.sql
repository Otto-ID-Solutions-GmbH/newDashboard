select count(distinct cuid)
from scan_actions
where
    created_at >= '2019-03-12 20:34:23'
  and created_at <= '2019-03-13 19:02:17'
  and (reader_id = 'cjpl2rdha002c90qyaq9o5nr0' or reader_id = 'cjpl2rdha002d90qy5bmdywuj')
  and cuid <> 'cjt5xrv1e00076wqyify0ovrp'
  and cuid <> 'cjt6x7uv20hhr6wqysxohevd5';

update items
set cycle_count = cycle_count - 585
where cuid in (
  select item_id
  from item_scan_action
  where scan_action_id = 'cjt6x7uv20hhr6wqysxohevd5'
);

update items
set cycle_count = cycle_count - 2292
where cuid in (
  select item_id
  from item_scan_action
  where scan_action_id = 'cjt5xrv1e00076wqyify0ovrp'
);

delete
from scan_actions
where
    created_at >= '2019-03-12 20:34:23'
  and created_at <= '2019-03-13 19:02:17'
  and (reader_id = 'cjpl2rdha002c90qyaq9o5nr0' or reader_id = 'cjpl2rdha002d90qy5bmdywuj')
  and cuid <> 'cjt5xrv1e00076wqyify0ovrp'
  and cuid <> 'cjt6x7uv20hhr6wqysxohevd5';


-- Clean up item statuses --

insert into temp_ids (cuid)
select distinct isa.old_status_id
from scan_actions sa
       inner join item_scan_action isa on sa.cuid = isa.scan_action_id
where isa.old_status_id is not null
  and (sa.reader_id = 'cjpl2rdha002c90qyaq9o5nr0' or sa.reader_id = 'cjpl2rdha002d90qy5bmdywuj')
  and sa.created_at >= '2019-03-12'
  and sa.created_at <= '2019-03-14';

create table item_statuses_temp
(
       cuid char(25) not null
              primary key,
       created_at timestamp null,
       updated_at timestamp null,
       notes text null,
       location_id char(25) null,
       location_type varchar(191) null,
       item_status_type_id char(25) not null,
       customer_id char(25) null,
       item_id char(25) not null,
       constraint item_statuses_customer_id_foreign_1
              foreign key (customer_id) references customers (cuid)
                     on delete cascade,
       constraint item_statuses_item_id_foreign_1
              foreign key (item_id) references items (cuid)
                     on delete cascade,
       constraint item_statuses_item_status_type_id_foreign_1
              foreign key (item_status_type_id) references item_status_types (cuid)
                     on delete cascade
)
       collate=utf8mb4_unicode_ci;


insert into item_statuses (cuid, created_at, updated_at, notes, location_id, location_type, item_status_type_id, customer_id, item_id)
select
       cuid, created_at, updated_at, notes, location_id, location_type, item_status_type_id, customer_id, item_id
from item_statuses
where item_statuses.cuid not in (select temp_ids.cuid from temp_ids);

