<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMemberIdBig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_user', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
        Schema::table('member_message', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('userId')->change();
            $table->unsignedBigInteger('fromId')->change();
        });
        Schema::table('member_favorite', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('userId')->change();
        });
        Schema::table('member_oauth', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_upload', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('userId')->change();
            $table->unsignedBigInteger('dataId')->change();
        });
        Schema::table('member_upload_category', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('pid')->change();
            $table->unsignedBigInteger('userId')->change();
        });
        Schema::table('member_address', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_money_cash', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_money', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_money_log', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_credit', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_credit_log', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_money_charge_order', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_vip_order', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
        Schema::table('member_deleted', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
        Schema::table('member_meta', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('memberUserId')->change();
        });
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
