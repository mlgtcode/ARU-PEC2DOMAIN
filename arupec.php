<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>ARUPEC</title>
      <link rel="stylesheet" href="../dist/chota.css" />
      <style>
         body.dark {
         --bg-color: #000;
         --bg-secondary-color: #131316;
         --font-color: #f5f5f5;
         --color-grey: #ccc;
         --color-darkGrey: #777;
         }
      </style>
   </head>
   <body>
      <div id="top" class="container" role="document">
         <header role="banner">
            <h1 class="pull-right" style="margin: 0;">
               <a href="javascript:void(0)" onclick="switchMode(this)">☀️</a>
            </h1>
            <h1>Validator: Aruba PEC su dominio</h1>
            <div class="clearfix"></div>
         </header>
         <main role="main">
            <section id="forms">
               <form method="get">
                  <fieldset id="forms__input">
                     <legend>Domain</legend>
                     <p>
                        <label for="input__webaddress">Web Address</label>
                        <input
                           id="input__webaddress"
                           placeholder="yoursite.com"
                           name="input__webaddress"
                           />
                     <hr>
                     <button class="button primary">Submit</button>
                     </p>
                  </fieldset>
                  <fieldset id="forms__checkbox">
                     <legend>Result</legend>
                     <?php
                     if (!function_exists("is_valid_domain_name")) {
                         function is_valid_domain_name($domain_name)
                         {
                             return preg_match(
                                     "/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i",
                                     $domain_name
                                 ) &&
                                 preg_match("/^.{1,253}$/", $domain_name) &&
                                 preg_match(
                                     "/^[^\.]{1,63}(\.[^\.]{1,63})*$/",
                                     $domain_name
                                 );
                         }
                     }
                     $invalid = "0";

                     $dom = is_valid_domain_name($_GET["input__webaddress"]);
                     if ($dom === true) {
                         $hostname = $_GET["input__webaddress"];
                     } else {
                         $hostname = "none";
                         $invalid = "1";
                     }

                     if ($invalid === "0") {
                         $mxhosts = [];
                         $weight = [];
                         $notok = "false";
                         echo "🌐&nbsp; Domain: " .
                             htmlspecialchars($hostname) .
                             "<br>";
                         if (getmxrr($hostname, $mxhosts, $weight)) {
                             for ($i = 0; $i < count($mxhosts); $i++) {
                                 if ($mxhosts[$i] == "mx.pec.aruba.it") {
                                     echo "✅&nbsp; MX record is pointing to mx.pec.aruba.it<br>";
                                     if ($weight[$i] === 10) {
                                         echo "✅&nbsp; Prio is set o 10.";
                                     } else {
                                         echo "🔘&nbsp; Prio is not set to 10.";
                                         break;
                                     }
                                 } else {
                                     echo "⛔️&nbsp; NOT OK! MX record is NOT pointing to mx.pec.aruba.it. It is actually pointing to" .
                                         htmlspecialchars($mxhosts[$i]) .
                                         ", Prio: " .
                                         htmlspecialchars($weight[$i]);
                                     $notok = "true";
                                 }
                             }
                         } else {
                             echo "⛔️&nbsp; No MX record found.";
                         }

                         $dns = dns_get_record($hostname, DNS_TXT);
                         echo "<br>";
                         // var_dump($dns);
                         foreach ($dns as $record) {
                             if ($record["txt"] == "v=spf1 a mx -all") {
                                 echo "✅&nbsp; SPF record OK!";
                             } else {
                                 echo "🔘&nbsp; SPF record likely not as suggested.";
                                 break;
                             }
                         }
                     } else {
                         echo "No result.";
                     }

				?>

                  </fieldset>
                  <fieldset id="forms__checkbox">
                     <legend>Example config</legend>
                     <pre>demo.tld   MX   mx.pec.aruba.it. 10
demo.tld   TXT  "v=spf1 a mx -all"
</pre>
                  </fieldset>
               </form>
            </section>
            <!-- <hr> -->
         </main>
      </div>
      <script src="../dist/main.js"></script>
   </body>
</html>
