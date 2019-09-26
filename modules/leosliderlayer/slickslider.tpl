<div class="your-class rev_slider {$sliderParams.slider_class}banner">
    {foreach from=$sliders item=slider}
        {if isset($slider.layersparams)}
            {foreach from=$slider.layersparams item=layer}
                {if $layer.layer_type == "image"}
                <div><img class="slide-image" src="{$sliderImgUrl}{$layer.layer_content}" alt="{$slider.title}"/></div>
                {/if}
            {/foreach}
        {/if}
    {/foreach}
</div>
<style>
    .slide-image{
/*        width:1250px; */
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
      $('.your-class').slick({
        dots: true,
  infinite: true,
  speed: 300,
  fade: true,
  slidesToShow: 1,
  slidesToScroll: 1,
  autoplay: true,
  autoplaySpeed: 3000,
  //respondTo: 'min',
  arrows: true
      });
    });
</script>