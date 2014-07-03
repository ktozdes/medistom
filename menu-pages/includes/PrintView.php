<?php
class PrintView{
	private $fileName;
	private $viewMode;
	private $printSum;
	private $pageNum;
	private $lineNum;
	private $sectionNames;
	private $maxLine;
	private $headerFooterData;
	
	function __construct($options,$sectionNames=null)
	{
		if (isset($options['file'])){
			$this->fileName = $options['file'];
			$this->viewMode = ($options['viewmode']=='print')?'print':'view';
			if ($options['viewmode']=='print'){
				$this->printPageHeader($options);
				$this->printViewCss();
				$this->lineNum = 0;
			}
			$this->maxLine = (!isset($options['maxLine']))?80:$options['maxLine'];
			$this->sectionNames = $sectionNames;
			$this->printSum = ($options['printsum']==true)?$options['printsum']:false;
			$this->pageNum = 1;
			
		}
		else 
			$this->halt('output file is not not provided found');
	}
	
	function __destruct(){
		//this part was commented about, because of json
		//echo '</table></div></body></html>';
	}
	
	public function printHeader($options,$result=null,$customResult=null,$customLineNum=1)
	{
		$customResult['page'] = $this->pageNum-1;
		//output header for printing
		if ($this->viewMode=='print'){
			$section = 'print_'.((isset($options[0])) ? $options[0]:$this->sectionNames['header']);
			require $this->fileName;
			$this->lineNum+=2;
		}
		//output summary
		if ($this->printSum==true && $this->pageNum>1){
			$section = ((isset($options[0])) ? $options[0]:$this->sectionNames['header']).'Sum';
			require $this->fileName;
			$this->lineNum+=2;
		}
		$section= ((isset($options[0])) ? $options[0]:$this->sectionNames['header']);
		require $this->fileName;
		$this->lineNum+=$customLineNum;
	}
	
	public function printFooter($options,$result=null,$customResult=null,$customLineNum=1)
	{
		//checks if headerAndFooterData exists
		if (!isset($result) && isset($this->headerFooterData))
			$result = $this->headerFooterData;
			
		//output header for printing
		$customResult['page'] = $this->pageNum;
		//output summary
		if ($this->printSum==true){
			$section = ((isset($options[0])) ? $options[0]:$this->sectionNames['footer']).'Sum';
			require $this->fileName;
		}
		$section = ((isset($options[0])) ? $options[0]:$this->sectionNames['footer']);
		require $this->fileName;
		if ($this->viewMode=='print'){
			$section = 'print_'.((isset($options[0])) ? $options[0]:$this->sectionNames['footer']);
			require $this->fileName;
		}
		$this->pageNum++;
		$this->lineNum=0;
	}
	
	public function printHtml($options,$result=null,$customResult=null,$customLineNum=1)
	{
		if (is_array($options)){
            foreach($options as $section){
                if (count($result)>0 || count($result) > 0){
                    //checks for paper size fittable line number
                    if ($this->viewMode=='print' && $this->lineNum > $this->maxLine){
                        $this->printPageBreak(null,$result,$customResult);
                    }
                }
                require $this->fileName;
                $this->lineNum++;
            }
        }
        else{
            $section = $options;
            if (count($result)>0 || count($result) > 0){
            //checks for paper size fittable line number
                if ($this->viewMode=='print' && $this->lineNum > $this->maxLine){
                    $this->printPageBreak(null,$result,$customResult);
                }
            }
            require $this->fileName;
            $this->lineNum++;
        }
	}
	
	public function printPageBreak($options,$result=null,$customResult=null)
	{
		$this->printFooter($options,(isset($this->headerFooterData))?$this->headerFooterData:$result,$customResult);
		$this->pageBreak();
		$this->printHeader(array_slice($options,1,1),(isset($this->headerFooterData))?$this->headerFooterData:$result,$customResult);
	}
	public function printPagination()
	{
		
	}
	
	public function setPages($startPage,$finishPage)
	{
		
	}
	
	public function helloworld()
	{
		echo 'hello world from print view class';
	}
	public function setHeaderData($params)
	{
		$this->headerFooterData = $params;
	}
	
	private function halt($message){
		echo 'Print error occurred: '.$message;
		die( " Session halted." );
	}
	
	public function printHeaderSum($section,$result,$customResult)
	{
		
	}
	
	public function printFooterSum($section,$result,$customResult)
	{
		
	}

	private function printPageHeader($customResult)
	{
		require 'views/print_page_header.php';
	}

	private function pageBreak()
	{
		require 'views/print_page_break.php';
	}

	private function printPageFooter($customResult)
	{
		require 'views/print_page_footer.php';
	}

	private function printViewCss()
	{
		require 'views/view_for_printing_css.php';
	}
}
?>