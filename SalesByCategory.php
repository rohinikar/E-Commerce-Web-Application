
<?php

$connection = oci_connect($username = 'sritapa',
                          $password = 'Electromec123',
                          $connection_string = '//oracle.cise.ufl.edu/orcl');
$query = " select c.cname,p.categoryid,count(pu.productid) from step2shopify_product p,step2shopify_purchase pu,step2shopify_category c
 where pu.productid=p.productid and c.CATEGORYID=p.CATEGORYID group by p.CATEGORYID,c.CNAME";
$result = oci_parse($connection, $query);
oci_execute($result);
$all=oci_fetch_all($result,$res);
$query1 = " select c.cname,p.categoryid,count(pu.productid) from step2shopify_product p,step2shopify_purchase pu,step2shopify_category c
 where pu.productid=p.productid and c.CATEGORYID=p.CATEGORYID group by p.CATEGORYID,c.CNAME";
$result1 = oci_parse($connection, $query1);
oci_execute($result1);
$count=1;
$fp = fopen('sales.json', 'w');
fwrite($fp,'{ "name": "flare", "children": [ { "name": "analytics", "children": [ { "name": "sales", "children": [');
while($row = oci_fetch_array($result1, OCI_BOTH))
{
	fwrite($fp,'{');
	if($all==$count)
	fwrite($fp,'"name":"'.$row[0].'","size":'.$row[2].'}');
	else
	fwrite($fp,'"name":"'.$row[0].'","size":'.$row[2].'},');
	$count=$count+1;
}
fwrite($fp,']}]}]}');
fclose($fp);
session_start();
$name= $_SESSION['name'];
$_SESSION['name']=$name;
?>


<html><head><title>Modify Products Page</title>
<meta charset="utf-8"
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>

text {
  font: 10px sans-serif;
}

</style>
<title>Step2Shopify</title>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">

<div id="container">
<div id="top_div">
<div class="heading">
<h1 align="LEFT"><a href="login.php"><img src="img/logo.jpg" width="187" height="50" alt="" class="img-thumbnail"/></a>   
Step2Shopify</h1></div></div>

<div class="topcorner">

        <h2 class="form-signin-heading"> &nbsp; &nbsp;WELCOME!!!</h2>
		<h2><img src="img/admin.jpg" width="75" height="50" alt="" class="img-thumbnail"/><?php print("\n".$name) ?></h2>
		
<div class="btn-group" style="padding: 230px; position:absolute; top:-49%">
<button type="button" class="btn btn-success" onclick="window.location.href='logout.php';"> <h5>LogOut</h5></button>
  </div>
		
</div>

  
<div class="btn-group" style="padding: 120px; position:absolute; top:3%">
<button type="button" class="btn btn-success" onclick="window.location.href='admin.php';"> <h5>Home</h5></button>
</div>  
</div>
</div>
</head>
<body background="img/background9.jpg">
<div id="bottom_div">

<script src="http://d3js.org/d3.v3.min.js"></script>
<script>

var diameter = 500,
    format = d3.format(",d"),
    color = d3.scale.category20c();

var bubble = d3.layout.pack()
    .sort(null)
    .size([diameter, diameter])
    .padding(1.5);

var svg = d3.select("#bottom_div").append("svg")
    .attr("width", diameter)
    .attr("height", diameter)
    .attr("class", "bubble");

d3.json("sales.json", function(error, root) {
  var node = svg.selectAll(".node")
      .data(bubble.nodes(classes(root))
      .filter(function(d) { return !d.children; }))
    .enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

  node.append("title")
      .text(function(d) { return d.className + ": " + format(d.value); });

  node.append("circle")
      .attr("r", function(d) { return d.r; })
      .style("fill", function(d) { return color(d.packageName); });

  node.append("text")
      .attr("dy", ".3em")
      .style("text-anchor", "middle")
      .text(function(d) { return d.className.substring(0, d.r / 3); });
});

// Returns a flattened hierarchy containing all leaf nodes under the root.
function classes(root) {
  var classes = [];

  function recurse(name, node) {
    if (node.children) node.children.forEach(function(child) { recurse(node.name, child); });
    else classes.push({packageName: name, className: node.name, value: node.size});
  }

  recurse(null, root);
  return {children: classes};
}

d3.select(self.frameElement).style("height", diameter + "px");

</script>
</div>


</body>
</html>
