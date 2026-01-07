<?php
	namespace App\adminmodel;
	use Illuminate\Database\Eloquent\Model;

	class SymbolMaster extends Model
	{
	   	protected $table ="m_symbol";
		protected $primaryKey ='SYMBOL_ID'; 
		protected $fillable = [
     	'SYMBOL_NO',
		'SYMBOL_DES',
        'SYMBOL_HDES',
     	'SYMBOL_BMP',
     	'SYMBOL_HFOCDES',
     	'Ind_Symbol',
     	'Symbol_Img',
     	'CONTENT_TYPE'
     ];
	 
      protected $guarded = ['SYMBOL_ID'];
    public function partydetails()
    	{
        return $this->belongsTo('App\model\PartyMaster','SYMBOL_NO','PARTYSYM');
    	}
	}
