<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        //テストケース実行前にフォルダデータを作成する
        $this->seed('FoldersTableSeeder');
    }

    public function due_date_should_be_date()
    {
        $response = $this->post('/folders/1/tasks/create', [
            'title' => 'Sample task',
            'due_date' => 123, //不正なデータですよ。
        ]);

        $response->assertSessionHasErrors([
            'due_date' => '期限日には日付を入力してください。',
        ]);

    }

    public function due_date_should_not_be_past()
    {
        $response = $this->post('/folders/1/tasks/create', [
            'title' => 'Sample task',
            // 不正なデータ（昨日の日付）
            'due_date' => Carbon::yesterday()->format('Y/m/d'),
        ]);
        $response->assertSessionHasErrors([
            'due_date' => '期限日 には今日以降の日付を入力してください。',
        ]);
    }

    public function status_should_be_within_defined_numbers()
    {
        $this->seed('TasksTableSeeder');

        $response = $this->post('/folders/1/tasks/1/edit', [
            'title' => 'Sample task',
            'due_date' => Carbon::today()->format('Y/m/d'),
            'status' => 999,
        ]);

        $response->assertSessionHasErrors([
            'status' => '状態には 未着手、着手中、完了のいずれかを指定してください。',
        ]);
    }
}