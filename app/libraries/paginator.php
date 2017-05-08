<?php
 class paginator
 {

    var $ipp = 250;
    var $numrows = 0;
    var $totalpages = 0;
    var $currentpage = 0;
    var $segment = 0;
    var $limit = "";

    function __construct($nb)
    {
        $this->nb = $nb;
    }

    function init($table,$segment = 4,$where = "")
    {
				
				$arg_count = func_num_args();
				$this->segment = $segment;
				$this->totalpages = ceil($this->numrows / $this->ipp); 
				
				if($arg_count > 2)
				{
					$r = $this->nb->db->run("SELECT COUNT(*) as count FROM $table WHERE $where");
					$this->numrows = $r[0]["count"];
				}else{
					$r = $this->nb->db->query($sql, PDO::FETCH_ASSOC);
					$i = 0;
					$this->numrows = $r->rowCount();
				}
				
				if(is_numeric($this->nb->get_uri($this->segment))) {
					$this->currentpage = (int)$this->nb->get_uri($this->segment);
				} else {
					$this->currentpage = 1;
				} 
				
				if ($this->currentpage > $this->totalpages) {
					$this->currentpage = $this->totalpages;
				}
				
				if ($this->currentpage < 1) {
					$this->currentpage = 1;
				}
			
        $offset = ($this->currentpage-1) * $this->ipp;
        $this->limit = $offset . "," . $this->ipp;
    }

    function build_links($link,$simple = true)
    {
			
        if($this->numrows > $this->ipp)
        {    
            if($simple)
            {
							
                $html = "<nav aria-label='Page navigation'><ul class='pagination'>";
                $range = 3;
                $prevpage = $this->currentpage - 1;
                
                $html .= "<li><a href='$link$prevpage' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
                
                    for ($x = ($this->currentpage - $range); $x < (($this->currentpage + $range) + 1); $x++) {
                       if (($x > 0) && ($x <= $this->totalpages)) {
                          if ($x == $this->currentpage) {
                             $html .= " <li class='active'><a href='$link$x'>$x</a></li> ";
                          } else {
                            $html .= " <li><a href='$link$x'>$x</a></li>";
                          } // end else
                       } // end if 
                    } // end for
        

               
                    $nextpage = $this->currentpage + 1;
                    //$html .= " <a href='$link$nextpage' class='page next'><i class=\"fa fa-arrow-right\"></i></a> ";
                    $html .= "<li><a href='$link$nextpage' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";


            return $html . "</ul></nav>";

            }else{
                return $this->getPaginationString($this->currentpage,$this->numrows,$this->ipp,0,$link,"");
            }

        }

        
    }

function getPaginationString($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
{       
    //defaults
    if(!$adjacents) $adjacents = 1;
    if(!$limit) $limit = 15;
    if(!$page) $page = 1;
    if(!$targetpage) $targetpage = "/";
    
    //other vars
    $prev = $page - 1;                                  //previous page is page - 1
    $next = $page + 1;                                  //next page is page + 1
    $lastpage = ceil($totalitems / $limit);             //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;                              //last page minus 1
    
    /* 
        Now we apply our rules and draw the pagination object. 
        We're actually saving the code to a variable in case we want to draw it more than once.
    */
    $pagination = "";
    if($lastpage > 1)
    {   
        $pagination .= "<div class=\"pagination\">";

        //previous button
        if ($page > 1) 
            $pagination .= "<a href=\"$targetpage$pagestring$prev\" class='page'>Back</a>";
        else
            $pagination .= "<span class=\"disabled page\">First</span>";    
        
        //pages 
        if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
        {   
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination .= "<span class=\"page active\">$counter</span>";
                else
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\" class='page'>$counter</a>";                 
            }
        }
        elseif($lastpage >= 7 + ($adjacents * 2))   //enough pages to hide some
        {
            //close to beginning; only hide later pages
            if($page < 1 + ($adjacents * 3))        
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current page\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\" class='page'>$counter</a>";                 
                }
                $pagination .= "<span class=\"elipses page\">...</span>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\" class='page'>$lpm1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\" class='page'>$lastpage</a>";       
            }
            //in middle; hide some front and some back
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "1\" class='page'>1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "2\" class='page'>2</a>";
                $pagination .= "<span class=\"elipses page\">...</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"active page\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\" class='page'>$counter</a>";                 
                }
                $pagination .= "<span class=\"elipses page\">...</span>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\" class='page'>$lpm1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\" class='page'>$lastpage</a>";       
            }
            //close to end; only hide early pages
            else
            {
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "1\" class='page'>1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "2\" class='page'>2</a>";
                $pagination .= "<span class=\"elipses page\">...</span>";
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current page\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\" class='page'>$counter</a>";                 
                }
            }
        }
        
        //next button
        if ($page < $counter - 1) 
            $pagination .= "<a href=\"" . $targetpage . $pagestring . $next . "\" class='page'>next</a>";
        else
            $pagination .= "<span class=\"disabled page\">next</span>";
        $pagination .= "</div>\n";
    }
    
    return $pagination;

}


 }
?>