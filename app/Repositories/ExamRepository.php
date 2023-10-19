<?php


namespace App\Repositories;


use App\Models\_3a_exam_result;
use App\Models\_3a_exam_result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB;

class ExamRepository extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return _3a_exam_result::class;
    }

    public static function getRankChart(int $userId, int $majorId, bool $hasSub = false): array
    {
        if ($hasSub) {
            return DB::select("
                                    SELECT 3a_exams.title,AVG(3a_exam_results.exam_ranking_data) as averageRank,slope*(@x:=@x+1)+intercept as regression_point
                                    FROM 3a_exam_results
                                    INNER JOIN 3a_exams ON 3a_exam_results.exam_id=3a_exams.id
                                    INNER JOIN (select id from users where exists
                                        (select * from users as reserved_user
                                            inner join bonyad_parents on reserved_user.id = bonyad_parents.parent_id
                                            where users.id = bonyad_parents.user_id and bonyad_parents.parent_id = {$userId} and reserved_user.deleted_at is null)
                                            and exists (select * from roles inner join role_user on roles.id = role_user.role_id
                                        where users.id = role_user.user_id and name = 'bonyadEhsanUser')
                                        and users.deleted_at is null and users.major_id={$majorId}
                                        ) as user_ids ON 3a_exam_results.user_id = user_ids.id
                                    CROSS JOIN (SELECT @x := 0) AS dummy1
                                    CROSS JOIN (
                                        select slope,y_bar_max - x_bar_max * slope as intercept
                                        from (
                                            select sum((x - x_bar) * (y - y_bar)) / sum((x - x_bar) * (x - x_bar)) as slope,max(x_bar) as x_bar_max,max(y_bar) as y_bar_max
                                                FROM (
                                                    select x,averageRank as y,AVG(x) over() as x_bar,AVG(averageRank) over() as y_bar
                                                        FROM (
                                                            SELECT AVG(exam_ranking_data) as averageRank,(@cnt := @cnt + 1) AS x
                                                                FROM 3a_exam_results CROSS JOIN (SELECT @cnt := 0) AS dummy
                                                                INNER JOIN (select id from users where exists
                                                                                (select * from users as reserved_user
                                                                                    inner join bonyad_parents on reserved_user.id = bonyad_parents.parent_id
                                                                                    where users.id = bonyad_parents.user_id and bonyad_parents.parent_id = {$userId} and reserved_user.deleted_at is null)
                                                                    and exists (select * from roles inner join role_user on roles.id = role_user.role_id
                                                                    where users.id = role_user.user_id and name = 'bonyadEhsanUser')
                                                                    and users.deleted_at is null and users.major_id={$majorId}
                                                                    ) as user_ids ON 3a_exam_results.user_id = user_ids.id
                                                                GROUP BY exam_id
                                                        ) s
                                                ) d
                                        ) a
                                    ) as regression
                                    GROUP BY exam_id,3a_exams.title;

            ");
        } else {
            return DB::select("
                                    SELECT 3a_exams.title,AVG(3a_exam_results.exam_ranking_data) as averageRank,slope*(@x:=@x+1)+intercept as regression_point
                                    FROM 3a_exam_results
                                    INNER JOIN 3a_exams ON 3a_exam_results.exam_id=3a_exams.id AND 3a_exam_results.user_id = {$userId}
                                    CROSS JOIN (SELECT @x := 0) AS dummy1
                                    CROSS JOIN (
                                        select slope,y_bar_max - x_bar_max * slope as intercept
                                        from (
                                            select sum((x - x_bar) * (y - y_bar)) / sum((x - x_bar) * (x - x_bar)) as slope,max(x_bar) as x_bar_max,max(y_bar) as y_bar_max
                                                FROM (
                                                    select x,averageRank as y,AVG(x) over() as x_bar,AVG(averageRank) over() as y_bar
                                                        FROM (
                                                            SELECT AVG(exam_ranking_data) as averageRank,(@cnt := @cnt + 1) AS x
                                                                FROM 3a_exam_results CROSS JOIN (SELECT @cnt := 0) AS dummy
                                                                where user_id={$userId}
                                                                GROUP BY exam_id
                                                        ) s
                                                ) d
                                        ) a
                                    ) as regression
                                    GROUP BY exam_id,3a_exams.title;

        ");
        }
    }

    public static function getUserRank(int $userId, int $majorId, bool $hasSub = false)
    {
        if ($hasSub) {
            return DB::select("SELECT 3a_exams.title,exam_ranking_data,exam_lesson_data
                                    FROM 3a_exam_results INNER JOIN 3a_exams ON 3a_exam_results.exam_id=3a_exams.id
                                    INNER JOIN (select id from users where exists
                                                (select * from users as reserved_user
                                                        inner join bonyad_parents on reserved_user.id = bonyad_parents.parent_id
                                                        where users.id = bonyad_parents.user_id and bonyad_parents.parent_id = {$userId} and reserved_user.deleted_at is null)
                                                and exists (select * from roles inner join role_user on roles.id = role_user.role_id
                                                                where users.id = role_user.user_id and name = 'bonyadEhsanUser')
                                                and users.deleted_at is null and users.major_id={$majorId}
                                             ) as user_ids ON 3a_exam_results.user_id = user_ids.id
                                    ;");
        } else {
            return DB::select("SELECT 3a_exams.title,exam_ranking_data,exam_lesson_data
                                    FROM 3a_exam_results INNER JOIN 3a_exams ON 3a_exam_results.exam_id=3a_exams.id AND 3a_exam_results.user_id = {$userId}
                                    ;");
        }

    }

    public static function getAverageRank(int $userId, int $majorId, bool $hasSub = false)
    {
        if ($hasSub) {
            return DB::select("SELECT AVG(exam_ranking_data) as data FROM 3a_exam_results
                                  INNER JOIN (select id from users where exists
                                                (select * from users as reserved_user
                                                        inner join bonyad_parents on reserved_user.id = bonyad_parents.parent_id
                                                        where users.id = bonyad_parents.user_id and bonyad_parents.parent_id = {$userId} and reserved_user.deleted_at is null)
                                                and exists (select * from roles inner join role_user on roles.id = role_user.role_id
                                                                where users.id = role_user.user_id and name = 'bonyadEhsanUser')
                                                and users.deleted_at is null and users.major_id={$majorId}
                                             ) as user_ids ON 3a_exam_results.user_id = user_ids.id;");
        } else {
            return DB::select("SELECT AVG(exam_ranking_data) as data FROM 3a_exam_results where user_id={$userId}");
        }

    }
}
