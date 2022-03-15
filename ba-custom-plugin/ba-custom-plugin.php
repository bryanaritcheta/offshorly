<?php
/**
 * Plugin Name: Countries Plugin
 * Plugin URI: https://restcountries.com/
 * Description: Plugin for showing a filterable widget of countries.
 * Version: 1.0.0
 * Author: Bryan Aritcheta
 * Author URI: https://www.bryanaritcheta.com
 */



// Assets
function my_enqueued_assets() {
    wp_enqueue_script('my-js-file', plugin_dir_url(__FILE__) . 'js/ba-scripts.js', '', time());
    wp_enqueue_style('my-css-file', plugin_dir_url(__FILE__) . 'css/ba-styles.css', '', time());
}
add_action('wp_enqueue_scripts', 'my_enqueued_assets');



// function that runs when shortcode is called
function ba_country_shortcode() {
  ob_start();
  
  $url = "https://restcountries.com/v3.1/all";
  $json = file_get_contents($url);
  $data = json_decode($json);
  
  ?>
  	<!-- Widget Container -->
    <div class="widget-container">
  
  	  <!-- Styles -->
      <style>
      .country-menu select {
        width: 100%;
      }
      .country-information .information-box {
        display: none;
        padding: 1rem;
      }
      .country-information .information-box.default,
      .country-information .information-box.active {
        display: flex;
      }
      .information-text {
        width: 45%;
      }
      .information-image {
        width: 55%;
        padding-left: 1rem;
      }
      .information-image img {
        width: 100%;
      }
      </style>
  	  <!-- /Styles -->
  
  	  <!-- Scripts -->
      <script>
      jQuery( document ).ready(function($) {
        // Handler for sorting of options
        $(".country-menu select").click(function() {
          var my_options = $(".country-menu select option");
          my_options.sort(function(a,b) {
            if (a.text > b.text) return 1;
            else if (a.text < b.text) return -1;
            else return 0;
          });
          $(".country-menu select").empty().append(my_options).selectpicker("refresh");
        });

        // Handler for default country to show
        $(".widget-container .country-information div.information-box:first-child").addClass("default");

        // Handler for filtering country to show
        $(".country-menu select").change(function(){
          var value =  $(".country-menu select option:selected");
          //console.log($.trim(value.text()));
          $(this).closest(".widget-container").find(".country-information .information-box").removeClass("default");
          $(this).closest(".widget-container").find(".country-information .information-box").removeClass("active");
          $(this).closest(".widget-container").find(".country-information #" + value.text().replaceAll(' ','')).addClass("active");
          //console.log($.trim(value.text()));
        });
      });
      </script>
  	  <!-- /Scripts -->
  
  		<!-- Country Menu -->
        <div class="country-menu">
          <select>
            <?php
  			  sort($data);
              foreach ($data as &$value) {
                $name = $value->name->common;
                ?>
                    <option value="<?php echo trim($name) ?>"><?php echo trim($name); ?></option>
                <?php
              }
            ?>
          </select>
        </div>
  		<!-- /Country Menu -->
  
  		<!-- Country Information -->
  		<div class="country-information">
              <?php
                foreach ($data as &$value) {
                  $name = $value->name->common;
                  $nameid = str_replace(' ', '', $name);
                  $timezone = $value->timezones;
                  $currency = (array)$value->currencies;
                  $flagurl = $value->flags->png;
                  ?>                  
                  <div class="information-box" id=<?php echo $nameid ?>>
                  	<div class="information-text">
                      <h3>
                        <?php
                            echo $name;
                        ?>
                      </h3>
                  	  <!-- Timezones -->
                      <ul>
                        <?php
                  			foreach ($timezone as $timezones) {
                              ?>
                              	<li>
                                  <?php
                                      echo $timezones;
                                  ?>
                              	</li>
                              <?php
                  			}
                        ?>
                      </ul>
                  	  <!-- /Timezones -->
                  	  <!-- Currencies -->
                      <ul>
                        <?php
                  			foreach ($currency as $key => $value) {
                              ?>
                              	<li>
                                  <?php
                                      echo $value->name;
                                  ?>
                              	</li>
                              <?php
                  			}
                        ?>
                      </ul>
                  	  <!-- /Currencies -->
                  </div>
                  <div class="information-image">
                  	  <!-- Flag -->
                  	  <img src=
                  		<?php
                  			echo $flagurl
                  		?>
                  	  />
                  	  <!-- /Flag -->
                  </div>
                  </div>
                  <?php
                }
              ?>
  		</div>
  		<!-- /Country Information -->
  	</div>
  <!-- /Widget Container-->
  <?php
  
  return ob_get_clean();
}

// register shortcode
add_shortcode('ba_country_plugin', 'ba_country_shortcode');