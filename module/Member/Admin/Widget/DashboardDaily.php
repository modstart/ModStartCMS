<?php

namespace Module\Member\Admin\Widget;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Widget\AbstractRawWidget;
use ModStart\Widget\Traits\HasRequestTrait;
use ModStart\Widget\Traits\HasVueTrait;
use Module\Member\Model\MemberUser;

class DashboardDaily extends AbstractRawWidget
{
    use HasRequestTrait;
    use HasVueTrait;

    public static function listUserCount($startDate, $endDate)
    {
        $startTs = strtotime($startDate);
        $endTs = strtotime($endDate);
        $endTs = min($endTs, time());
        $startTs = min($startTs, $endTs);
        $startTs = max($startTs, $endTs - 365 * 86400);
        $startDate = date('Y-m-d 00:00:00', $startTs);
        $endDate = date('Y-m-d 23:59:59', $endTs);
        $records = [];
        for ($ts = $startTs; $ts <= $endTs; $ts += 86400) {
            $day = date('Y-m-d', $ts);
            $records[$day] = [
                'day' => $day,
                'count' => 0,
            ];
        }
        $result = MemberUser::query()
            ->selectRaw('DATE(created_at) as day, count(*) as cnt')
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('day')
            ->get();
        foreach ($result as $row) {
            $records[$row->day]['count'] = $row->cnt;
        }
        return [
            'start' => $startDate,
            'end' => $endDate,
            'records' => array_values($records),
        ];
    }

    public function permit()
    {
        return AdminPermission::permit('\Module\Member\Admin\Controller\MemberDashboardController@index');
    }

    public function request()
    {
        $input = InputPackage::buildFromInput();
        $data = self::listUserCount(
            $input->getDate('start'),
            $input->getDate('end')
        );
        return Response::generateSuccessData($data);
    }

    public function script()
    {
        $start = date('Y-m-d', strtotime('-30 day'));
        $end = date('Y-m-d');
        return <<<JS
new Vue({
    el: '#{$this->id()}',
    data(){
        return {
            loading:false,
            dateRange:['{$start}','{$end}'],
            records:[],
            pickerOptions: {
              shortcuts: [
                {
                    text: '本周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * (start.getDay() - 1));
                        picker.\$emit('pick', [start, end]);
                    }
                },
                {
                    text: '上周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * (start.getDay() + 6));
                        end.setTime(end.getTime() - 3600 * 1000 * 24 * (end.getDay()));
                        picker.\$emit('pick', [start, end]);
                    }
                },
                {
                    text: '本月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setDate(1);
                        picker.\$emit('pick', [start, end]);
                    }
                },
                {
                    text: '上月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setMonth(start.getMonth() - 1);
                        start.setDate(1);
                        end.setDate(0);
                        picker.\$emit('pick', [start, end]);
                    }
                },
                {
                    text: '最近一周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.\$emit('pick', [start, end]);
                    }
                },
                {
                    text: '最近一个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.\$emit('pick', [start, end]);
                    }
                },
                {
                    text: '最近三个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.\$emit('pick', [start, end]);
                    }
                }
              ]
            },
        }
    },
    watch:{
        dateRange:{
            handler:function(){
                this.doLoad();
            },
            deep:true
        }
    },
    mounted(){
        this.doLoad();
    },
    methods: {
        doLoad(){
            this.loading = true;
            MS.widget.requestInContainer(this, {
                scope: 'admin',
                data:{
                    start: this.dateRange[0],
                    end: this.dateRange[1],
                },
                success:(res)=>{
                    this.loading = false;
                    this.records = res.data.records;
                }
            })
        }
    }
})
JS;
    }

    public function template()
    {
        return <<<HTML
<div class="ub-panel margin-bottom">
    <div class="head">
        <div class="more" ref="submit">
            <el-date-picker
              v-model="dateRange"
              type="daterange"
              value-format="yyyy-MM-dd"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              :picker-options="pickerOptions"
              >
            </el-date-picker>
<!--            <el-button icon="el-icon-search" ref="submit" @click="doLoad"></el-button>-->
        </div>
        <div class="title">数据明细</div>
    </div>
    <div class="body" v-loading="loading">
        <table class="ub-table border">
            <thead>
            <tr>
                <th>日期</th>
                <th>注册用户数</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(r,rIndex) in records" :key="r.day">
                <td>{{r.day}}</td>
                <td>{{r.count}}</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
HTML;
    }

}
