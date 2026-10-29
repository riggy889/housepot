<?php

use App\Models\Expense;
use App\Models\ExpenseSplit;

test('expense factory creates a valid expense in pence', function () {
    $expense = Expense::factory()->create();
    $expense->refresh();

    expect($expense->amount)->toBeInt()->toBeGreaterThan(0);
    expect($expense->spent_on->format('H:i:s'))->toBe('00:00:00');
    expect($expense->version)->toBe(1);
});

test('expense split belongs to a parent expense and cascades on force delete', function () {
    $expenseSplit = ExpenseSplit::factory()->create();
    $expenseSplit->refresh();

    expect($expenseSplit->expense)->toBeInstanceOf(Expense::class);

    $expenseSplit->expense->forceDelete();

    expect(ExpenseSplit::find($expenseSplit->id))->toBeNull();
});
