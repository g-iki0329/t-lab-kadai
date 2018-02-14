<?php

namespace App\Libs;

use App\Http\Models\Activity;
use App\Http\Models\Page;
use App\Http\Models\User;

class PageUtility
{
    /**
     * 検索
     *
     * @param $keyword
     * @return array
     */
    public static function findUserViewedPage($keyword){
        $pages = Page::where('title', 'LIKE', "$keyword%")->get();
        $activities = Activity::all();
        $users = User::all();

        $userPageMap = [];
        $userPageArray = [];
        $notFoundPageArray = [];

        for ($i = 0; $i < count($pages); $i++) {
            $isFound = false;
            for ($j = 0; $j < count($activities); $j++) {
                if ($pages[$i]->id == $activities[$j]->page_id) {
                    for ($k = 0; $k < count($users); $k++) {
                        if ($users[$k]->id == $activities[$j]->user_id) {
                            if(array_key_exists($users[$k]->id, $userPageMap)
                                && array_key_exists($pages[$i]->id, $userPageMap[$users[$k]->id])){
                                $userPageMap[$users[$k]->id][$pages[$i]->id]['view_count'] =
                                    $userPageMap[$users[$k]->id][$pages[$i]->id]['view_count'] + 1;
                            }else{
                                $isFound = true;
                                $userPage = [];
                                $userPage['page_id'] = $pages[$i]->id;
                                $userPage['page_title'] = $pages[$i]->title;
                                $userPage['user_id'] = $users[$k]->id;
                                $userPage['user_name'] = $users[$k]->name;
                                $userPage['view_count'] = 1;
                                $userPageMap[$users[$k]->id][$pages[$i]->id] = $userPage;
                            }
                        }

                    }
                }
            }
            if (!$isFound) {
                $userPage = [];
                $userPage['page_id'] = $pages[$i]->id;
                $userPage['page_title'] = $pages[$i]->title;
                $notFoundPageArray[] = $userPage;
            }
        }

        // ユーザIDでソート
        ksort($userPageMap);

        foreach ($userPageMap as $pageMap) {
            foreach ($pageMap as $page) {
                $userPageArray[] = $page;
            }
        }

        foreach ($notFoundPageArray as $notFoundPage) {
            $userPageArray[] = $notFoundPage;
        }

        return $userPageArray;
    }
}