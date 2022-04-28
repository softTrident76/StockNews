<?php
class WhkPagination
{
	public $const_count_rows_per_page = 10;
	public $const_count_pagenaitions_per_block = 2;

	public $count_totals = 0;
	public $current_page_idx = 1;
	public $current_page_block_idx = 0;
	public $count_blocks = 0;
	public $count_in_block = 0;
	
	public $start_paginatin_no = 0;
	public $end_paginatin_no = 0;

	public $count_pages = 0;

	public function initialize($count_totals)
	{
		$this->count_totals = $count_totals;

		if(isset($_POST['currentblock_idx']))
			$this->current_page_block_idx = (int)$_POST['currentblock_idx'];
		if( $this->current_page_block_idx < 0 )
			$this->current_page_block_idx = 0;

		if(isset($_POST['currentpage_idx']))
			$this->current_page_idx = (int)$_POST['currentpage_idx'];
		if( $this->current_page_idx < 1 )
			$this->current_page_idx = 1;

		$div = (int)($this->count_totals / $this->const_count_rows_per_page);
		$mod = $this->count_totals % $this->const_count_rows_per_page;
		
		$this->count_pages = $mod == 0 ? $div: $div + 1;

		$div = (int)($this->count_pages / $this->const_count_pagenaitions_per_block);
		$mod = $this->count_pages % $this->const_count_pagenaitions_per_block;
		
		$this->count_blocks = $mod == 0 ? $div: $div + 1;

		// echo '<br> count_blocks = '. $this->count_blocks;
		// echo '<br>';
		
		$this->start_paginatin_no = ( $this->current_page_block_idx ) * $this->const_count_pagenaitions_per_block;
		$this->end_paginatin_no = ( $this->current_page_block_idx + 1) * $this->const_count_pagenaitions_per_block ;

		if($this->end_paginatin_no > $this->count_pages )
			$this->end_paginatin_no = $this->count_pages;

		$this->count_in_block = $this->end_paginatin_no - $this->start_paginatin_no;
	}

	public function render()
	{
		echo'
		<nav aria-label="Page navigation">
			<ul class="pagination">';
					if($this->current_page_block_idx <= 0)
					{
						echo '<li class="page-item disabled" id="first"><a class="page-link" href="javascript:click_pagination(\'first\');">|<</a></li>';
						echo '<li class="page-item disabled" id="prev"><a class="page-link" href="javascript:click_pagination(\'prev\');"><<</a></li>';
					}
					else 
					{
						echo '<li class="page-item" id="first"><a class="page-link" href="javascript:click_pagination(\'first\');">|<</a></li>';
						echo '<li class="page-item" id="prev"><a class="page-link" href="javascript:click_pagination(\'prev\');"><<</a></li>';
					}
					for($idx = $this->start_paginatin_no; $idx < $this->end_paginatin_no; $idx++ )
					{
						$pageno = $idx + 1;
						$pageid = "page". $pageno;
						if($pageno == $this->current_page_idx)
							echo '<li class="page-item active" id="'. $pageid .'"><a class="page-link" href="javascript:click_pagination(\''. $pageid .'\');">'. $pageno.'</a></li>';
						else
							echo '<li class="page-item" id="'. $pageid .'"><a class="page-link" href="javascript:click_pagination(\''. $pageid .'\');">'. $pageno.'</a></li>';

					}
					if($this->current_page_block_idx >= $this->count_blocks - 1)
					{
						echo '<li class="page-item disabled" id="next"><a class="page-link" href="javascript:click_pagination(\'next\');">>></a></li>';
						echo '<li class="page-item disabled" id="last"><a class="page-link" href="javascript:click_pagination(\'last\');">>|</a></li>';
					}
					else 
					{
						echo '<li class="page-item" id="next"><a class="page-link" href="javascript:click_pagination(\'next\');">>></a></li>';
						echo '<li class="page-item" id="last"><a class="page-link" href="javascript:click_pagination(\'last\');">>|</a></li>';
					}				
		echo' 
				</ul>
		</nav>';

		echo "
		<script>
			function click_pagination(obj) 
			{								
				var classList = $('#' + obj).attr('class').split(/\s+/);
				console.log(classList);

				if( jQuery.inArray('disabled', classList) >= 0 )
					return;

				console.log('click_pagination ' + obj + ' available');

				switch(obj) 
				{
					case 'first':
						var const_count_pagenaitions_per_block =  parseInt('". $this->const_count_pagenaitions_per_block. "');
						var current_page_block_idx = parseInt('". $this->current_page_block_idx. "');
						var count_blocks = parseInt('". $this->count_blocks."');
												
						if( current_page_block_idx == 0 )
							return;

						current_page_block_idx = 0;

						$('#currentpage_idx').val(current_page_block_idx * const_count_pagenaitions_per_block + 1);
						$('#currentblock_idx').val(current_page_block_idx);
						$('#campaign_form').submit();
					break;

					case 'last':
						var const_count_pagenaitions_per_block =  parseInt('". $this->const_count_pagenaitions_per_block. "');
						var current_page_block_idx = parseInt('". $this->current_page_block_idx. "');
						var count_blocks = parseInt('". $this->count_blocks. "');																		

						current_page_block_idx = count_blocks - 1;

						$('#currentpage_idx').val(current_page_block_idx * const_count_pagenaitions_per_block + 1);
						$('#currentblock_idx').val(current_page_block_idx);
						$('#campaign_form').submit();						
					break;

					case 'prev':
						var const_count_pagenaitions_per_block =  parseInt('". $this->const_count_pagenaitions_per_block. "');
						var current_page_block_idx = parseInt('". $this->current_page_block_idx. "');
						var count_blocks = parseInt('". $this->count_blocks. "');
						
						// console.log(current_page_block_idx);
						// console.log(count_blocks);

						if( current_page_block_idx == 0 )
							return;

						current_page_block_idx--;

						$('#currentpage_idx').val(current_page_block_idx * const_count_pagenaitions_per_block + 1);
						$('#currentblock_idx').val(current_page_block_idx);
						$('#campaign_form').submit();

					break;

					case 'next':
						var const_count_pagenaitions_per_block =  parseInt('". $this->const_count_pagenaitions_per_block. "');
						var current_page_block_idx = parseInt('". $this->current_page_block_idx. "');
						var count_blocks = parseInt('". $this->count_blocks. "');						

						// console.log(current_page_block_idx);
						// console.log(count_blocks);

						if( current_page_block_idx == count_blocks )
							return;

						current_page_block_idx++;

						$('#currentpage_idx').val(current_page_block_idx * const_count_pagenaitions_per_block + 1);
						$('#currentblock_idx').val(current_page_block_idx);
						$('#campaign_form').submit();
					break;

					default:
						var current_page_block_idx = parseInt('". $this->current_page_block_idx. "');
						idx = obj.slice(4);
						$('#currentpage_idx').val(idx);
						$('#currentblock_idx').val(current_page_block_idx);
						$('#campaign_form').submit();
					break;
				}
			}
		</script>
		";
	}
}
?>
