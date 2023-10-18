import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsLostDetailComponent } from './items-lost-detail.component';

describe('ItemsLostDetailComponent', () => {
  let component: ItemsLostDetailComponent;
  let fixture: ComponentFixture<ItemsLostDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsLostDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsLostDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
