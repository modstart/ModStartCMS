<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberVipPriceMarket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_vip_set', function (Blueprint $table) {
            /** @see \ModStart\Admin\Type\UploadType */
            $table->decimal('priceMarket', 20, 2)->nullable()->comment('划线价');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll(\Module\Member\Model\MemberVipSet::class, [
            'priceMarket' => \Illuminate\Support\Facades\DB::raw('price'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
