<script type="text/javascript" src="js/ddaccordion.js"></script>
<script type="text/javascript">
ddaccordion.init({
	headerclass: "submenuheader", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: false, //Collapse previous content (so only one open at any time)? true/false 
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: true, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: true, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["suffix", "<img src='images/plus.gif' class='statusicon' />", "<img src='images/minus.gif' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
               // alert(index);
	}
})
</script>
<style type="text/css">

.glossymenu{
margin: 0;
margin-bottom: 2px;
padding: 0;
width: 170px; /*width of menu*/
border: 0px solid #9A9A9A;
border-bottom-width: 0;
}

.glossymenu a.menuitem{
background: black url(images/menu-bg.jpg) repeat-x bottom left;
font: bold 14px "Lucida Grande", "Trebuchet MS", Verdana, Helvetica, sans-serif;
color: black;
display: block;
position: relative; /*To help in the anchoring of the ".statusicon" icon image*/
width: auto;
padding: 4px 0;
padding-left: 10px;
text-decoration: none;
}


.glossymenu a.menuitem:visited, .glossymenu .menuitem:active{
color: black;
}

.glossymenu a.menuitem .statusicon{ /*CSS for icon image that gets dynamically added to headers*/
position: relative;
/*top: 5px;
right: 5px;*/
border: none;
float: right;
padding-right: 5px;
}

.glossymenu a.menuitem:hover{
background-image: url(images/bottom-bg.jpg);
}

.glossymenu div.submenu{ /*DIV that contains each sub menu*/
    background: white;
}

.glossymenu div.submenu ul{ /*UL of each sub menu*/
    list-style-type: none;
    margin: 0;
    padding: 0;
}

.glossymenu div.submenu ul li{
 /*border-bottom: 1px solid blue;*/
}

.glossymenu div.submenu ul li a{
display: block;
font: normal 13px "Lucida Grande", "Trebuchet MS", Verdana, Helvetica, sans-serif;
color: black;
text-decoration: none;
padding: 2px 0;
padding-left: 10px;
}

.glossymenu div.submenu ul li a:hover{
background: #DFDCCB;
colorz: white;
}
.glossymenu div.submenu ul li ul{
	margin-left: 20px;
}
.glossymenu div.submenu ul li ul li{
	border: none;
	background:url(images/arrow.gif) no-repeat left;
	padding-left:10px;
}

</style>


<div class="glossymenu">
<!--<a class="menuitem submenuheader" href="admin.php?page=pages">Pages</a>
<div class="submenu">
	<ul> 
		<li><a href="admin.php?page=pages&id=1">Home page</a></li>
		<li><a href="admin.php?page=pages&id=2">About Us</a></li>
		<li><a href="admin.php?page=pages&id=3">Mission Vision</a></li>
		<li><a href="admin.php?page=pages&id=4">Contact Us</a></li>
	</ul>
</div>-->
<a class="menuitem submenuheader" href="admin.php?page=report">Statistics Report</a>
<div class="submenu">
	<ul>
<!--		<li><a href="admin.php?page=store-stat">Store Statistics</a></li>-->
                <li><a href="admin.php?page=store-stat-final">Statistics</a></li>
<!--                <li><a href="admin.php?page=store-stat-test">Store Statistics Testing</a></li>-->
                <li><a href="admin.php?page=day-store-stat">Advance Statistics</a></li>
<!--                <li><a href="admin.php?page=day-cashier-stat">Cashier Day Statistics</a></li>-->
	</ul>
</div>
<?php
if(permission(array('all', 'perform_manual_text'), $permission_tasks)){
?>
<a class="menuitem submenuheader" href="admin.php?page=report">Stat Manual Text</a>
<div class="submenu">
	<ul>
		<li><a href="admin.php?page=add-manual-text">Add New Text</a></li>
                <li><a href="admin.php?page=view-manual-text">View Text</a></li>
<!--                <li><a href="admin.php?page=missdata">Screen Time</a></li>-->
	</ul>
</div>
<?php
}
    
?>
<a class="menuitem submenuheader" href="#">Admin Features</a>
<div class="submenu">
	<ul>
            <?php
            if(permission(array('all', 'create_chain_admin','create_store_admin', 'create_cashier_admin'), $permission_tasks)){
//              if(permission(array('all'), $permission_tasks)){
            ?>
		<li><a href="admin.php?page=add-admin">Add New Admin User</a></li>
		<li><a href="admin.php?page=manage-admin">Manage Users</a></li>
                <?php 
                 if(permission(array('all', 'view_group_permissions'), $permission_tasks)){
                ?>
                <li><a href="admin.php?page=view-permission">Group Permission</a></li>
                <?php
                 }
            }   
                 ?>
            <li><a href="admin.php?page=change-password">Change Password</a></li>    
	</ul>
</div>
<?php
?>
</div>