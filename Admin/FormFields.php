<? $FormFields = [ "keys" => ["col", "type", "label"],
	"Cat" => [
		"fields" => [
			"catName" 			=> [4, "text",     	"Category Name"],
			"status" 			=> [2, "sel",     	"Status"],
		],
	],
	"Odr" => [
		"fields" => [
			"cusName" 		=> [3, 	"text", 		"Name"],
			"mob" 			=> [3, 	"number", 		"Mobile"],
			"mob2" 			=> [3, 	"number", 		"Alt. Mob."],
			"email" 		=> [3, 	"email", 		"Email"],
			"street1" 		=> [4, 	"text", 		"Address Line 1"],
			"street2" 		=> [4, 	"text", 		"Address Line 2"],
			"landmark" 		=> [4, 	"text", 		"Landmark"],
			"city" 			=> [4, 	"text", 		"Town / City"],
			"pincode" 		=> [4, 	"number", 		"Pincode"],
			"state" 		=> [4, 	"text", 		"State"],
			"amount" 		=> [3, 	"text", 		"Amount"],
			"pg" 			=> [3, 	"text", 		"Pay Method"],
			"discount" 		=> [3, 	"text", 		"Discount"],
			"remarks" 		=> [3, 	"text", 		"Remarks"],
		],
		"select" => [],
		"pgList" => [
			"Razor Pay" 	=> "<b> Razor Pay </b> <br> <small> Credit Card, Debit Card, Net Banking, UPI (GPay, PhonePe, BharatPe) </small>",
// 			"CC Avenue" 	=> "<b> CC Avenue </b> <br> <span class='pl-4'> Credit Card, Debit Card, Net Banking, UPI (GPay / PhonePe / BharatPe) </span>",
// 			"CashFree" 		=> "<b> CashFree </b> <br> <span class='pl-4'> Credit Card, Debit Card, Net Banking, UPI (GPay / PhonePe / BharatPe) </span>",
// 			"PayTm" 		=> "<b> PayTm </b> <br> <span class='pl-4'> Credit Card, Debit Card, Net Banking, UPI (GPay / PhonePe / BharatPe) </span>"
		],
		"states" => [
			"Andaman and Nicobar Islands",
			"Andhra Pradesh",
			"Andhra Pradesh (New)",
			"Arunachal Pradesh",
			"Assam",
			"Bihar",
			"Chandigarh",
			"Chattisgarh",
			"Dadra and Nagar Haveli",
			"Daman and Diu",
			"Delhi",
			"Goa",
			"Gujarat",
			"Haryana",
			"Himachal Pradesh",
			"Jammu and Kashmir",
			"Jharkhand",
			"Karnataka",
			"Kerala",
			"Lakshadweep Islands",
			"Madhya Pradesh",
			"Maharashtra",
			"Manipur",
			"Meghalaya",
			"Mizoram",
			"Nagaland",
			"Odisha",
			"Pondicherry",
			"Punjab",
			"Rajasthan",
			"Sikkim",
			"Tamil Nadu",
			"Telangana",
			"Tripura",
			"Uttar Pradesh",
			"Uttarakhand",
			"West Bengal"
		]
	],
	"Pdt" => [
		"fields" => [
			"catId" 			=> [2, "sel",     	"Category"],
			"pdtName" 			=> [4, "text",     	"Product Name"],
			"colour" 			=> [2, "text",     	"Colour"],
			"price" 			=> [2, "number",    "Price"],
			"status" 			=> [2, "sel",     	"Status"],
// 			"minQty" 			=> [2, "number",     	"Min. Qty."],
			// "pdtName2" 			=> [4, "text",     	"Name"],
			// "des" 				=> [4, "text",     	"Description"],
			// "qty" 				=> [1, "number",     	"Quantity"],
			// "srNo" 				=> [1, "text",     	"Sr. No."],
			// "sale" 				=> [1, "number",     	"Sale"],
			// "offer" 				=> [1, "number",     	"Offer"],
			// "discount" 			=> [1, "number",     	"Discount"],
			// "stock" 			=> [1, "sel",     	"Stock"],
		],
		"clrFields" => [
			"c1" 			=> [6, "text",     	"Colour"],
			"p1" 			=> [6, "text",     	"Product No."],
			"c2" 			=> [6, "text",     	""],
			"p2" 			=> [6, "text",     	""],
			"c3" 			=> [6, "text",     	""],
			"p3" 			=> [6, "text",     	""],
			"c4" 			=> [6, "text",     	""],
			"p4" 			=> [6, "text",     	""],
			"c5" 			=> [6, "text",     	""],
			"p5" 			=> [6, "text",     	""],
			"c6" 			=> [6, "text",     	""],
			"p6" 			=> [6, "text",     	""],
			"c7" 			=> [6, "text",     	""],
			"p7" 			=> [6, "text",     	""],
			"c8" 			=> [6, "text",     	""],
			"p8" 			=> [6, "text",     	""],
			"c9" 			=> [6, "text",     	""],
			"p9" 			=> [6, "text",     	""],
			"c10" 			=> [6, "text",     	""],
			"p10" 			=> [6, "text",     	""],
			"c11" 			=> [6, "text",     	""],
			"p11" 			=> [6, "text",     	""],
			"c12" 			=> [6, "text",     	""],
			"p12" 			=> [6, "text",     	""],
			"c13" 			=> [6, "text",     	""],
			"p13" 			=> [6, "text",     	""],
			"c14" 			=> [6, "text",     	""],
			"p14" 			=> [6, "text",     	""],
			"c15" 			=> [6, "text",     	""],
			"p15" 			=> [6, "text",     	""],
		],
	],
	/*_____________________________________________________________________ P A Y M E N T S ___________________________________ P A Y M E N T S ___________________________________ P A Y M E N T S ___________________________________*/
	// "Pay" => [
	// 	"fields" => [
	// 		"tDate" 			=> [2, 	"date", 		"Date"],
	// 		"srNo" 				=> [2, 	"number", 		"Vr.No."],
	// 		"name" 				=> [6, 	"text", 		"Name (Paid To)"],
	// 		"amount" 			=> [2, 	"number", 		"Amount(₹)"],
	// 		"serHead" 			=> [2, 	"sel",     		"For Service"],
	// 		"payFor" 			=> [2, 	"sel",     		"Sub Service"],
	// 		"payMode" 			=> [2, 	"sel",     		"Mode"],
	// 		"particulars" 		=> [6, 	"text",    		"Particulars"],
	// 		"chequeDate" 		=> [3, 	"date", 		"Cheque Date"],
	// 		"chequeNo" 			=> [3, 	"number", 		"Cheque No."],
	// 		"chequeBank" 		=> [6, 	"text", 		"Cheque Bank"],
	// 		"upiTrnDate" 		=> [3, 	"date",   		"UPI Transaction Date"],
	// 		"upiTrnId" 			=> [3, 	"number",   	"UPI Transaction Id (last 5 characters)"],
	// 	],
	// 	"select" => []
	// ],
	/*___________________________________________________________ P E R M I S S I O N S _________________________________ P E R M I S S I O N S _________________________________ P E R M I S S I O N S _________________________________*/
	"permissions" => [
	//	link / php page 		=> permission
		"Category" 				=> "perm1",
		"Category_Add" 			=> "perm2",
		"Category_Edit"			=> "perm3",

		"Products" 				=> "perm4",
		"Products_Add" 			=> "perm5",
		"Products_Edit"			=> "perm6",

		"Orders" 				=> "perm7",
		"Orders_Add" 			=> "perm8",
		"Orders_Edit"			=> "perm9",

		"Users" 				=> "perm10",
		"Users_Add" 			=> "perm11",
		"Users_Edit"			=> "perm12",
	],
	/*____________________________________________________________________ R E C E I P T S ___________________________________ R E C E I P T S ___________________________________ R E C E I P T S ___________________________________*/
	// "Rct" => [
	// 	"fields" => [
	// 		"tDate" 			=> [2, 	"date", 		"Date"],
	// 		"srNo" 				=> [2, 	"number", 		"Rt.No."],
	// 		"name" 				=> [6, 	"text", 		"Name (Received From)"],
	// 		"amount" 			=> [2, 	"number", 		"Amount(₹)"],
	// 		"serHead" 			=> [2, 	"sel",     		"For Service"],
	// 		"payFor" 			=> [2, 	"sel",     		"Sub Service"],
	// 		"payMode" 			=> [2, 	"sel",     		"Mode"],
	// 		"particulars" 		=> [6, 	"text",    		"Particulars"],
	// 		"chequeDate" 		=> [3, 	"date", 		"Cheque Date"],
	// 		"chequeNo" 			=> [3, 	"number", 		"Cheque No."],
	// 		"chequeBank" 		=> [6, 	"text",   		"Cheque Bank"],
	// 		"upiTrnDate" 		=> [3, 	"date",   		"UPI Transaction Date"],
	// 		"upiTrnId" 			=> [3, 	"number",   	"UPI Transaction Id (last 5 characters)"],
	// 	],
	// 	"select" => []
	// ],
	/*____________________________________________________________________ S E R V I C E S ___________________________________ S E R V I C E S ___________________________________ S E R V I C E S ___________________________________*/
	// "Ser" => [
	// 	"fields" => [
	// 		"remarks" 		=> [1, 	"number", 		"Sr. No."],
	// 		"serHead" 		=> [4, 	"sel", 			"Head Type"],
	// 		"serName" 		=> [4, 	"text", 		"Name"],
	// 		"serFor" 		=> [2, 	"sel", 			"Type"],
	// 	],
	// 	"select" => [
	// 		"serFor" => [
	// 			""      => " -- select type -- ",
	// 			"MH" 	=> "Main Head",
	// 			"Pay" 	=> "Payments",
	// 			"Rct" 	=> "Receipts"
	// 		]
	// 	]
	// ],
	/*______________________________________________________________________________________ U S E R S ___________________________________ U S E R S ___________________________________ U S E R S ___________________________________*/
	"Usr" => [
		"fields" => [
			"usertype" 				=> [2, "sel",   	"User Type"],
			"name" 					=> [2, "text",   	"Name"],
			"username" 				=> [1, "text",   	"Username"],
			"mob" 					=> [2, "number",    "Mobile"],
			"email" 				=> [3, "email",   	"Email"],
			"sesTimeout" 			=> [1, "number",   	"Timeout (s)"],
			"status" 				=> [1, "sel",   	"Status"],
		],
		"select" => [
			"usertype" => [
				"Admin",
				"Accountant",
				"BackOffice",
				"Clerk",
				"Dev",
				"Manager",
			],
		]
	],
	/*_____________________________________________________________________________________ U S E R   P E R M I S S I O N S ___________________________________ U S E R S   P E R M I S S I O N S ___________________________________*/
	"Usr_Prm" => [
		"fields" => [
			"perm1" 				=> [4, "chk",   		"Category --> View"],
			"perm2" 				=> [4, "chk",   		"Category --> Add"],
			"perm3" 				=> [4, "chk",   		"Category --> Edit"],

			"perm4" 				=> [4, "chk",   		"Products --> View"],
			"perm5" 				=> [4, "chk",   		"Products --> Add"],
			"perm6" 				=> [4, "chk",   		"Products --> Edit"],

			"perm7" 				=> [4, "chk",   		"Orders --> View"],
			"perm8" 				=> [4, "chk",   		"Orders --> Add"],
			"perm9" 				=> [4, "chk",   		"Orders --> Edit"],

			"perm10" 				=> [4, "chk",   		"Users --> View"],
			"perm11" 				=> [4, "chk",   		"Users --> Add"],
			"perm12" 				=> [4, "chk",   		"Users --> Edit"],
		]
	],
];
$sql = "SELECT * FROM Status ORDER BY sts";
$sts = array_column (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC), "sts");
$ACCOUNTS = $sts[0];
$ACTIVE = $sts[1];
$DELETED = $sts[2];
$DELIVERED = $sts[3];
$DISABLED = $sts[4];
$PAID = $sts[5];
$PAY_ERROR = $sts[6];
$PENDING = $sts[7];
$PROCESSING = $sts[8];
$SHIPPED = $sts[9];
$UTILITY = $sts[10];

$FormFields["Cat"]["select"]["status"] = $FormFields["Pdt"]["select"]["status"] = $FormFields["Usr"]["select"]["status"] = [$ACTIVE, $DISABLED];

$sql = "SELECT * FROM Cat WHERE status='$ACTIVE' ORDER BY catName";
$FormFields["Pdt"]["select"]["catId"] = array_column (mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC), "catName", "cat_id");

$sql = "SELECT * FROM Pdt WHERE status IN ('$ACTIVE','$DISABLED')";
$pts = mysqli_fetch_all (mysqli_query ($conn, $sql), MYSQLI_ASSOC);
$pt_ct = array_column ($pts, "catId", "pdt_id");
$pt_nm = array_column ($pts, "pdtName", "pdt_id");
$pt_pr = array_column ($pts, "price", "pdt_id");

$fields = []; ?>