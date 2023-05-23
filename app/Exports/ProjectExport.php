<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;  // 指定使用数组结构
use Maatwebsite\Excel\Concerns\WithMapping; // 设置excel中每列要展示的数据
use Maatwebsite\Excel\Concerns\WithHeadings; // 设置excel的首行对应的表头信息
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;

class ProjectExport implements FromArray, WithMapping, WithHeadings, Responsable
{
    use Exportable;
    protected $data;
    private $fileName;
    public function __construct(array $data)
    {
        $this->data     = $data; // 实例化该脚本的时候，需要传入要导出的数据
        $this->fileName = date('YmdHis') . '_Financial_project.xlsx'; // 指定导出的文件名
    }

    public function array(): array // 返回的数据
    {
        return $this->data;
    }
    public function map($value): array // 指定excel中每一列的数据字段
    {
        return [
            $value['business_principal_name'],
            $value['income_team'],
            $value['income_group'],
        ];
    }

    public function headings(): array // 指定excel的表头
    {
        return [
            '学校',
            '性别',
            '学生/教师组',
            '本科/专科',
            '姓名',
            '比赛类型',
            '比赛项目',
            '联系方式'
        ];
    }
}
