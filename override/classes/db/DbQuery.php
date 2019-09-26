<?php

class DbQuery extends DbQueryCore
{
    protected $query = array(
        'type'   => 'SELECT',
        'select' => array(),
        'from'   => '',
        'join'   => array(),
        'where'  => array(),
        'where_or' => array(),
        'group'  => array(),
        'having' => array(),
        'order'  => array(),
        'limit'  => array('offset' => 0, 'limit' => 0),
    );
    
    public function whereOr($restriction)
    {
        if (!empty($restriction)) {
            $this->query['where_or'][] = $restriction;
        }
    
        return $this;
    }
    
    public function build()
    {
        if ($this->query['type'] == 'SELECT') {
            $sql = 'SELECT '.((($this->query['select'])) ? implode(",\n", $this->query['select']) : '*')."\n";
        } else {
            $sql = $this->query['type'].' ';
        }
    
        if (!$this->query['from']) {
            throw new PrestaShopException('Table name not set in DbQuery object. Cannot build a valid SQL query.');
        }
    
        $sql .= 'FROM '.implode(', ', $this->query['from'])."\n";
    
        if ($this->query['join']) {
            $sql .= implode("\n", $this->query['join'])."\n";
        }
    
        if ($this->query['where']) {
            $sql .= 'WHERE ('.implode(') AND (', $this->query['where']).")\n";
        }

        if ($this->query['where_or']) {
            $sql .= 'WHERE ('.implode(') OR (', $this->query['where_or']).")\n";
        }
        
        if ($this->query['group']) {
            $sql .= 'GROUP BY '.implode(', ', $this->query['group'])."\n";
        }
    
        if ($this->query['having']) {
            $sql .= 'HAVING ('.implode(') AND (', $this->query['having']).")\n";
        }
    
        if ($this->query['order']) {
            $sql .= 'ORDER BY '.implode(', ', $this->query['order'])."\n";
        }
    
        if ($this->query['limit']['limit']) {
            $limit = $this->query['limit'];
            $sql .= 'LIMIT '.($limit['offset'] ? $limit['offset'].', ' : '').$limit['limit'];
        }
    
        return $sql;
    }
    
}