if(typeof(jQuery)=="undefined"){/*alert("Joliprint notification : jQuery is missing, please check your Wordpress configuration.")*/}else{jQuery(document).ready(function(){(function(b){b.fn.wp_joliprint=function(c){var d={};if(joliprint_button_config){b.extend(d,joliprint_button_config)}return this.each(function(){if(c){b.extend(d,c)}if(d!=null){var e=null;if(d.skipJoliprintCss!=null&&d.skipJoliprintCss==true){joliprint=new joliPrint({skipJoliprintCss:true})}else{joliprint=new joliPrint()}joliprint.set("service","wp-plugin");b.each(d,function(g,h){if(g!=null&&g.length>0&&g.charAt(0)!="_"&&h!=null){joliprint.set(g,h)}});var f=joliprint.getHtml().innerHTML;b(this).html(f)}})}})(jQuery);if(typeof(joliPrint)!="undefined"){var a=false;if(typeof(joliprint_button_config)!="undefined"&&joliprint_button_config!=null&&joliprint_button_config._hide!=null&&joliprint_button_config._hide!=false){a=true}if(a==true){return}jQuery(".joliprint_button").each(function(d,h){try{var f=jQuery(h);if(f==null||f.length==0){return}if(typeof(f.wp_joliprint)=="undefined"){console.log("Problem : Joliprint plugin for jQuery is not loaded.");return}var g={};var c=jQuery(this).attr("data-url");if(typeof(c)=="undefined"||c==null||c.length==0){c=jQuery("meta[name='joliprint.url']").attr("content")}if(typeof(c)=="undefined"||c==null||c.length==0){c=document.location.href}g.url=c;f.wp_joliprint(g);var k=jQuery(this).attr("data-title");if(typeof(k)=="undefined"||k==null||k.length==0){k=jQuery("meta[name='joliprint.title']").attr("content")}if(typeof(k)=="undefined"||k==null||k.length==0){k=c}var j=(typeof(joliprint_button_config)!="undefined"&&joliprint_button_config!=null&&joliprint_button_config._ga_tracking!=null?joliprint_button_config._ga_tracking:true);if(typeof(j)=="undefined"||j==null||j!=true){return}if(typeof(_gaq)!="undefined"&&_gaq!=null){f.find("a").bind("click",function(i){try{_gaq.push(["_trackEvent","Joliprint","Print PDF",k])}catch(i){console.log(i.message)}})}else{if(typeof(pageTracker)!="undefined"&&pageTracker!=null){f.find("a").bind("click",function(i){try{pageTracker._trackEvent("Joliprint","Print PDF",k)}catch(i){console.log(i.message)}})}else{}}}catch(b){console.log(b)}})}})};