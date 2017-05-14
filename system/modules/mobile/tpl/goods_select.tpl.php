<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/11/9
 * Time: 16:13
 */
$js = G_TEMPLATES_JS;
return <<<HTML
<div class="selectBar-wrap">
    <div class="selectBar">
        <a id="selectBar-close" href="javascript:;"></a>
        <div class="goodsInfo">
            <div class="goodsInfo-wrap">
                <div class="goodsInfo-img">
                    <script src="{$js}/mobile/goodsselect.js" type="text/javascript"></script>'
                    <input type="hidden" class="shopId" value="{$shopinfo['id']}"/>
                    <img src="G_UPLOAD_PATH/{$shopinfo['thumb']}" alt="goodsImg" />
		        </div>
		        <div class="goodsInfo-detail">
                    <p class="detail-price">￥<span>{$shopinfo['money']}</span></p>
                    <p class="detail-last">库存：<span>{$shopinfo['surplus']}</span>件</p>
                    <p class="detail-selected">请选择：<span>{$spec_name}</span></p>
		        </div>
		    </div>
		</div>
        <div class="goodsClass">
            <div class="goodsClass-wrap">
		    </div>
		</div>
		<div class="goodsNums">
		    <div class="goodsNums-wrap">
                <h3>购买数量</h3>
                <div class="numSelBar">
                <a href="javascript:;" id="Less">-</a><span id="nums">1</span><a href="javascript:;" id="More">+</a>
                </div>
            </div>
        </div>

HTML;
